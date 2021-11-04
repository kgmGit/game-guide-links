<?php

namespace Tests\Feature\Game;

use App\Models\Favorite;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_クエリパラメータ無し(): void
    {
        $user1 = User::factory()->create();

        $user1->games()->create(['title' => 'aaa']);

        $response = $this->json('GET', 'api/games');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => []
        ]);
    }

    public function test正常系_クエリパラメータ有り_お気に入り有り(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        /** @var Game $game */
        $game = $user1->games()->create(['title' => 'aabbcc']);

        $game->favorites()->saveMany([
            $user1->favorites()->make(),
            $user2->favorites()->make(),
        ]);

        $response = $this->json('GET', 'api/games?title=bb');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'aabbcc',
                    'favorites_count' => 2,
                ]
            ]
        ]);
    }

    public function test正常系_クエリパラメータ有り_条件一致６以上(): void
    {
        $user1 = User::factory()->create();

        $user1->games()->createMany([
            ['title' => 'ccc'],
            ['title' => 'aabb'],
            ['title' => 'bbaa'],
            ['title' => 'bbaacc'],
            ['title' => 'aa4'],
            ['title' => 'aa5'],
            ['title' => 'aa6'],
        ]);

        $response = $this->json('GET', 'api/games?title=aa');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'id' => 2,
                    'title' => 'aabb',
                    'favorites_count' => 0,
                ],
                [
                    'id' => 3,
                    'title' => 'bbaa',
                    'favorites_count' => 0,
                ],
                [
                    'id' => 4,
                    'title' => 'bbaacc',
                    'favorites_count' => 0,
                ],
                [
                    'id' => 5,
                    'title' => 'aa4',
                    'favorites_count' => 0,
                ],
                [
                    'id' => 6,
                    'title' => 'aa5',
                    'favorites_count' => 0,
                ],
            ]
        ]);
    }

    public function test正常系_クエリパラメータ有り_ゲーム登録なし(): void
    {
        $response = $this->json('GET', 'api/games?title=bb');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => []
        ]);
    }
}
