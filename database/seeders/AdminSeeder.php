<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
{
    \App\Models\User::create([
        'name' => 'Resepsionis',
        'email' => 'admin@hotel.com',
        'password' => bcrypt('admin123'),
        'role' => 'admin'
    ]);
}
}
