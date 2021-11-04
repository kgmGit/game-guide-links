<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'email' => 'test@test.com',
        ]);
        User::factory()->unverified()->create([
            'email' => 'test2@test.com',
        ]);
        User::factory()->count(8)->create();

        $users = User::all();
        $games = Game::factory()->count($users->count() * 10)->make();
        $count = 1;
        foreach ($users as $user) {
            $user->games()->saveMany(
                $games->forPage($count, 10)
            );
            $count += 1;
        }

        $users_verified = User::whereNotNull('email_verified_at')->get();
        foreach ($games as $game) {
            $favorites = $users_verified->random(5)->map(function ($user) {
                return $user->favorites()->make();
            });
            $game->favorites()->saveMany($favorites);
        }
    }
}
