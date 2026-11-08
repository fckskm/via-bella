<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 20);   // tour | guide | destination | hotel
            $table->string('ref', 120);   // slug / код
            $table->timestamps();
            $table->unique(['user_id', 'kind', 'ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
