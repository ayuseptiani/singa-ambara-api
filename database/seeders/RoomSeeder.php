<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'slug' => 'panji',
                'name' => 'Panji Room',
                'category' => 'Superior',
                'price' => 450000,
                'description' => 'Dirancang dengan sentuhan modern yang efisien, Panji Room menawarkan kenyamanan tanpa kompromi.',
                'facilities' => ["24 m² Size", "Queen Bed", "Smart TV", "Rain Shower"],
                'image' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=2070&auto=format&fit=crop'
            ],
            [
                'slug' => 'lovina',
                'name' => 'Lovina Room',
                'category' => 'Deluxe',
                'price' => 650000,
                'description' => 'Menghadirkan ruang yang lebih lega dengan nuansa relaksasi. Dilengkapi balkon pribadi.',
                'facilities' => ["32 m² Size", "King Bed", "Private Balcony", "Work Desk"],
                'image' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?q=80&w=1974&auto=format&fit=crop'
            ],
            [
                'slug' => 'buleleng',
                'name' => 'Buleleng Suite',
                'category' => 'Suite',
                'price' => 1250000,
                'description' => 'Definisi kemewahan tertinggi. Dilengkapi ruang tamu terpisah, bathtub, dan amenitas premium.',
                'facilities' => ["56 m² Size", "Bathtub", "Living Room", "Minibar"],
                'image' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=2070&auto=format&fit=crop'
            ]
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}