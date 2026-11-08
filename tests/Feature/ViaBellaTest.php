<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Guide;
use App\Models\Review;
use App\Models\Schedule;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViaBellaTest extends TestCase
{
    use RefreshDatabase;

    private function makeTour(int $capacity = 3): array
    {
        $guide = Guide::create(['name' => 'Тест Гид', 'slug' => 'test', 'country' => 'italy', 'city' => 'Рим', 'specialization' => 'История', 'languages' => 'ru', 'description' => 'd']);
        $tour = Tour::create(['guide_id' => $guide->id, 'title' => 'Тест', 'slug' => 'test-tour', 'description' => 'd', 'country' => 'italy', 'location' => 'Рим', 'category' => 'culture', 'duration' => 1, 'price' => 100, 'publication_status' => 'published']);
        $schedule = Schedule::create(['tour_id' => $tour->id, 'starts_at' => now()->addDays(10), 'ends_at' => now()->addDays(10)->addHours(5), 'capacity' => $capacity, 'status' => 'open']);

        return [$tour, $schedule];
    }

    private function payload(Schedule $s, int $n): array
    {
        return ['schedule_id' => $s->id, 'participants_count' => $n, 'contact_name' => 'Иван', 'contact_email' => 'i@example.com', 'contact_phone' => '+7 900 000 00 00', 'agree' => 1];
    }

    public function test_public_pages_render(): void
    {
        $this->seed();
        foreach (['/', '/tury', '/gidy', '/napravleniya', '/karta', '/konstruktor', '/aviabilety', '/oteli', '/vdohnovenie', '/vhod', '/registratsiya', '/izbrannoe'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/tury/amalfi-golden-hour')->assertOk()->assertSee('Амальфи');
    }

    public function test_guest_cannot_book(): void
    {
        [$tour, $s] = $this->makeTour();
        $this->post("/tury/{$tour->slug}/bronirovanie", $this->payload($s, 1))->assertRedirect('/vhod');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_never_exceeds_capacity(): void
    {
        [$tour, $s] = $this->makeTour(3);
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->actingAs($a)->post("/tury/{$tour->slug}/bronirovanie", $this->payload($s, 2))->assertRedirect();
        $this->assertSame(1, $s->fresh()->freeSeats());

        // Превышение отклоняется сервером
        $this->actingAs($b)->post("/tury/{$tour->slug}/bronirovanie", $this->payload($s, 2))->assertSessionHasErrors('participants_count');
        $this->assertSame(2, Booking::sum('participants_count'));

        // Последнее место можно занять, дальше — нет
        $this->actingAs($b)->post("/tury/{$tour->slug}/bronirovanie", $this->payload($s, 1))->assertRedirect();
        $this->assertSame(0, $s->fresh()->freeSeats());
        $this->actingAs($a)->post("/tury/{$tour->slug}/bronirovanie", $this->payload($s, 1))->assertSessionHasErrors('participants_count');
        $this->assertSame(3, Booking::sum('participants_count'));
    }

    public function test_cancelled_booking_returns_seats(): void
    {
        [$tour, $s] = $this->makeTour(2);
        $u = User::factory()->create();
        $this->actingAs($u)->post("/tury/{$tour->slug}/bronirovanie", $this->payload($s, 2));
        $this->assertSame(0, $s->fresh()->freeSeats());
        $this->actingAs($u)->post('/kabinet/bronirovaniya/' . Booking::first()->id . '/otmena')->assertRedirect();
        $this->assertSame(2, $s->fresh()->freeSeats());
    }

    public function test_schedule_must_belong_to_tour_and_validation_runs(): void
    {
        [$tour, $s] = $this->makeTour();
        $other = Tour::create(['guide_id' => $tour->guide_id, 'title' => 'Другой', 'slug' => 'other', 'description' => 'd', 'country' => 'italy', 'location' => 'Рим', 'category' => 'sea', 'duration' => 1, 'price' => 50]);
        $u = User::factory()->create();
        $this->actingAs($u)->post("/tury/{$other->slug}/bronirovanie", $this->payload($s, 1))->assertSessionHasErrors('schedule_id');
        $this->actingAs($u)->post("/tury/{$tour->slug}/bronirovanie", ['schedule_id' => $s->id, 'participants_count' => 0])->assertSessionHasErrors(['participants_count', 'contact_name', 'contact_email', 'agree']);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_user_sees_only_own_bookings_and_done_page_is_protected(): void
    {
        [$tour, $s] = $this->makeTour(5);
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->actingAs($a)->post("/tury/{$tour->slug}/bronirovanie", $this->payload($s, 1));
        $booking = Booking::first();
        $this->actingAs($b)->get("/bronirovanie/{$booking->id}")->assertForbidden();
        $this->actingAs($b)->post("/kabinet/bronirovaniya/{$booking->id}/otmena")->assertForbidden();
    }

    public function test_review_only_after_completed_trip_and_once(): void
    {
        [$tour, $future] = $this->makeTour();
        $past = Schedule::create(['tour_id' => $tour->id, 'starts_at' => now()->subDays(5), 'ends_at' => now()->subDays(4), 'capacity' => 5, 'status' => 'open']);
        $u = User::factory()->create();
        $mk = fn ($sc) => Booking::create(['reference' => 'VB-' . uniqid(), 'user_id' => $u->id, 'schedule_id' => $sc->id, 'participants_count' => 1, 'status' => 'confirmed', 'unit_price' => 100, 'total_price' => 100, 'contact_name' => 'x', 'contact_email' => 'x@x.x', 'contact_phone' => '123456']);
        $upcoming = $mk($future);
        $done = $mk($past);
        $data = ['rating' => 5, 'text' => 'Прекрасная поездка, всем рекомендую!'];

        $this->actingAs($u)->post("/bronirovaniya/{$upcoming->id}/otzyv", $data)->assertSessionHasErrors('text');
        $this->actingAs($u)->post("/bronirovaniya/{$done->id}/otzyv", ['rating' => 9, 'text' => 'коротко'])->assertSessionHasErrors(['rating', 'text']);
        $this->actingAs($u)->post("/bronirovaniya/{$done->id}/otzyv", $data)->assertRedirect();
        $this->assertSame(1, Review::count());
        $this->actingAs($u)->post("/bronirovaniya/{$done->id}/otzyv", $data)->assertSessionHasErrors('text');
        $this->assertSame(1, Review::count());
    }

    public function test_admin_area_is_restricted(): void
    {
        $this->get('/admin')->assertRedirect('/vhod');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertOk();
    }

    public function test_passwords_are_hashed_and_registration_validates(): void
    {
        $this->post('/registratsiya', ['name' => 'Анна', 'email' => 'anna@example.com', 'password' => 'Secret1234', 'password_confirmation' => 'Secret1234'])->assertRedirect();
        $user = User::where('email', 'anna@example.com')->first();
        $this->assertNotSame('Secret1234', $user->password);
        $this->assertTrue(password_verify('Secret1234', $user->password));
        $this->post('/vyhod');
        $this->post('/registratsiya', ['name' => 'A', 'email' => 'bad', 'password' => '123', 'password_confirmation' => '1'])->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_catalog_filters(): void
    {
        $this->seed();
        $this->get('/tury?country=greece')->assertOk()->assertSee('Санторини: белое и синее')->assertDontSee('Гальштат и Зальцкаммергут');
        $this->get('/tury?kind=excursion&theme=historical')->assertOk();
        $this->get('/tury?q=несуществующее')->assertOk()->assertSee('Ничего не нашли');
        $this->get('/tury?guests=500')->assertSessionHasErrors('guests');
    }

    public function test_draft_tours_are_hidden(): void
    {
        [$tour] = $this->makeTour();
        $tour->update(['publication_status' => 'draft']);
        $this->get('/tury/test-tour')->assertNotFound();
    }
}
