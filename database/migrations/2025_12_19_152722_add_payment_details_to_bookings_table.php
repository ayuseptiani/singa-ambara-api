<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            
            // 1. BUAT KOLOM GUEST NAME DULU (Ini yang bikin error sebelumnya karena tidak ada)
            if (!Schema::hasColumn('bookings', 'guest_name')) {
                $table->string('guest_name')->after('user_id')->nullable();
            }

            // 2. Baru buat Phone Number (Sekarang aman karena guest_name sudah ada)
            if (!Schema::hasColumn('bookings', 'phone_number')) {
                $table->string('phone_number')->after('guest_name')->nullable();
            }

            // 3. Cek Payment Method
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->after('total_price')->default('Bayar di Hotel');
            }

            // 4. Cek Payment Status
            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status')->after('payment_method')->default('pending');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'phone_number', 'payment_method', 'payment_status']);
        });
    }
};