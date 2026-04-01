<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Genre;
use App\Models\Film;
use App\Models\Room;
use App\Models\Seat;
use App\Models\FilmSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin & Test Users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        User::factory(10)->create();

        // 2. Create Genres
        $genres = collect(['Action', 'Drama', 'Comedy', 'Sci-Fi', 'Horror', 'Romance'])
            ->map(fn($name) => Genre::create(['name' => $name]));

        // 3. Create Rooms and Seats
        Room::factory(5)->create()->each(function ($room) {
            // Create 20 seats for each room
            for ($i = 1; $i <= 20; $i++) {
                Seat::create([
                    'room_id' => $room->id,
                    'seat_number' => 'S-' . $i,
                ]);
            }
        });

        // 4. Create Films and attach Genres
        Film::factory(10)->create()->each(function ($film) use ($genres) {
            $film->genres()->attach(
                $genres->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        // 5. Create Film Sessions
        $films = Film::all();
        $rooms = Room::all();

        foreach ($films as $film) {
            FilmSession::create([
                'film_id' => $film->id,
                'room_id' => $rooms->random()->id,
                'language' => collect(['arabic', 'french', 'english'])->random(),
                'start_time' => now()->addDays(rand(1, 5)),
                'end_time' => now()->addDays(rand(1, 5))->addHours(2),
                'price' => rand(40, 100),
            ]);
        }
    }
}