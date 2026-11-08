<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('country', 30)->index();
            $table->string('city', 80);
            $table->string('specialization');
            $table->string('languages');
            $table->text('description');              // короткое описание
            $table->text('story')->nullable();         // личная история
            $table->string('quote')->nullable();
            $table->string('contact_information')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(1);
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
