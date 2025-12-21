<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // ID unik (panji, lovina)
            $table->string('name');
            $table->string('category');
            $table->decimal('price', 12, 0); // Harga tanpa desimal
            $table->text('description');
            $table->string('image');
            $table->json('facilities'); // Menyimpan array fasilitas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};