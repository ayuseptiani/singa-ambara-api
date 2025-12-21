<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // Menyimpan ID User yang booking
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Menyimpan ID Kamar yang dibooking
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('total_price');
            $table->string('status')->default('confirmed'); // Langsung confirm aja dulu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};