<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index()
    {
        return view('admin.tours.index', ['tours' => Tour::with('guide')->withCount('schedules')->latest()->get()]);
    }

    public function create()
    {
        return view('admin.tours.form', ['tour' => new Tour(['kind' => 'tour', 'publication_status' => 'draft', 'duration' => 1]), 'guides' => Guide::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $tour = Tour::create($data);

        return redirect()->route('admin.tours.edit', $tour)->with('status', 'Тур создан. Теперь добавьте даты.');
    }

    public function edit(Tour $tour)
    {
        return view('admin.tours.form', ['tour' => $tour->load('schedules'), 'guides' => Guide::orderBy('name')->get()]);
    }

    public function update(Request $request, Tour $tour)
    {
        $tour->update($this->data($request));

        return back()->with('status', 'Изменения сохранены.');
    }

    /** Тур с бронированиями не удаляется физически — переводится в архив. */
    public function destroy(Tour $tour)
    {
        if ($tour->bookings()->exists()) {
            $tour->update(['publication_status' => 'archived']);

            return redirect()->route('admin.tours.index')->with('status', 'У тура есть бронирования — он перенесён в архив.');
        }
        $tour->delete();

        return redirect()->route('admin.tours.index')->with('status', 'Тур удалён.');
    }

    private function data(Request $request): array
    {
        $d = $request->validate([
            'guide_id' => ['required', 'exists:guides,id'],
            'title' => ['required', 'string', 'max:160'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:5000'],
            'country' => ['required', 'in:' . implode(',', array_keys(config('via.countries')))],
            'location' => ['required', 'string', 'max:120'],
            'kind' => ['required', 'in:tour,excursion'],
            'category' => ['required', 'in:' . implode(',', array_keys(config('via.categories')))],
            'theme' => ['nullable', 'in:' . implode(',', array_keys(config('via.themes')))],
            'duration' => ['required', 'integer', 'min:1', 'max:60'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'publication_status' => ['required', 'in:draft,published,archived'],
            'cover' => ['nullable', 'string', 'max:200'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);
        if ($request->hasFile('photo')) {
            $name = Str::random(12) . '.' . $request->file('photo')->extension();
            $request->file('photo')->move(public_path('uploads'), $name);
            $d['cover'] = 'uploads/' . $name;
        }
        unset($d['photo']);

        return $d;
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'tour';
        $slug = $base;
        $i = 2;
        while (Tour::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
