<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya 1 ruangan
        Room::firstOrCreate(
            ['name' => 'Ruang Rapat Utama'], // unique key
            [
                'capacity' => 10,
                'location' => 'Lantai 3',
                'remarks'  => 'LCD Projector & Whiteboard',
            ]
        );
    }
}
