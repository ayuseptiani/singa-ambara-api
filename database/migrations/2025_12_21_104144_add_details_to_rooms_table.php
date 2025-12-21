<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Kolom Stok Kamar (Default kita anggap ada 5 kamar per tipe)
            $table->integer('total_units')->default(5)->after('price');
            
            // Kolom Kapasitas Orang (Default 2 orang)
            $table->integer('capacity')->default(2)->after('total_units');
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['total_units', 'capacity']);
        });
    }
};