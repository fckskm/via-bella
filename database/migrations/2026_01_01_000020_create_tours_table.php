<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description');
            $table->string('country', 30)->index();
            $table->string('location', 120);            // город / регион
            $table->string('kind', 20)->default('tour')->index();   // tour | excursion
            $table->string('category', 30)->index();    // sea, mountains, culture, gastro, wellness, signature
            $table->string('theme', 30)->nullable()->index();       // cultural, gastronomic, historical, nature, active
            $table->unsignedSmallInteger('duration');   // дней (для экскурсий — 1)
            $table->string('duration_label', 40)->nullable();
            $table->decimal('price', 10, 2);
            $table->string('publication_status', 20)->default('published')->index(); // draft | published | archived
            $table->string('cover')->nullable();
            $table->json('gallery')->nullable();
            $table->json('program')->nullable();
            $table->json('included')->nullable();
            $table->decimal('lat', 8, 5)->nullable();
            $table->decimal('lng', 8, 5)->nullable();
            $table->string('accent', 20)->default('pistachio');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
