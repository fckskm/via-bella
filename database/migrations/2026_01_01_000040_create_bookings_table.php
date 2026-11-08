<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 16)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('participants_count');
            $table->string('status', 20)->default('pending')->index(); // pending | confirmed | cancelled
            $table->decimal('unit_price', 10, 2);   // стоимость на момент оформления
            $table->decimal('total_price', 10, 2);
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone', 40);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
