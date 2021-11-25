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

    public function test正常系_検索文字列無し(): void
    {
        $user1 = User::factory()->create();

        $user1->games()->create(['title' => 'aaa']);

        $response = $this->json('GET', 'api/games');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => []
        ]);
    }

    public function test正常系_検索文字列有り_お気に入り有り_未ログイン(): void
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

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'aabbcc',
                    'favorites_count' => 2,
                    'favorited' => false,
                    'owner' => false,
                ]
            ]
        ]);
    }

    public function test正常系_検索文字列有り_お気に入り有り_お気に入り済み(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();

        /** @var Game $game */
        $game = $user1->games()->create(['title' => 'aabbcc']);

        $this->assertEquals($game->user_id, $user1->id);

        $game->favorites()->saveMany([
            $user1->favorites()->make(),
        ]);

        $this->actingAs($user1);
        $response = $this->json('GET', 'api/games?title=bb');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'aabbcc',
                    'favorites_count' => 1,
                    'favorited' => true,
                    'owner' => true,
                ]
            ]
        ]);
    }

    public function test正常系_検索文字列有り_お気に入り有り_お気に入りしていない(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        /** @var Game $game */
        $game = $user1->games()->create(['title' => 'aabbcc']);

        $game->favorites()->saveMany([
            $user2->favorites()->make(),
        ]);

        $this->actingAs($user1);
        $response = $this->json('GET', 'api/games?title=bb');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'aabbcc',
                    'favorites_count' => 1,
                    'favorited' => false,
                    'owner' => true,
                ]
            ]
        ]);
    }

    public function test正常系_検索文字列有り_作成者でない(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        /** @var Game $game */
        $game = $user2->games()->create(['title' => 'aabbcc']);

        $this->actingAs($user1);
        $response = $this->json('GET', 'api/games?title=bb');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'aabbcc',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ]
            ]
        ]);
    }

    public function test正常系_検索文字列有り_条件一致６以上(): void
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

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'title' => 'aabb',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
                [
                    'id' => 3,
                    'title' => 'bbaa',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
                [
                    'id' => 4,
                    'title' => 'bbaacc',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
                [
                    'id' => 5,
                    'title' => 'aa4',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
                [
                    'id' => 6,
                    'title' => 'aa5',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
            ]
        ]);
    }

    public function test正常系_検索文字列有り_ゲーム登録なし(): void
    {
        $response = $this->json('GET', 'api/games?title=bb');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => []
        ]);
    }

    public function test正常系_検索文字列有り_未ログイン_所有者なしゲーム(): void
    {
        Game::factory()->create(['title' => 'bb']);
        $response = $this->json('GET', 'api/games?title=bb');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'bb',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ]
            ]
        ]);
    }

    public function test正常系_ページ指定あり(): void
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

        $response = $this->json('GET', 'api/games?title=aa&page=2');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 7,
                    'title' => 'aa6',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
            ]
        ]);
    }

    public function test正常系_ページ指定あり_ページ番号が不正の場合１ページ目を表示(): void
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

        $response = $this->json('GET', 'api/games?title=aa&page=hoge');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'title' => 'aabb',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
                [
                    'id' => 3,
                    'title' => 'bbaa',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
                [
                    'id' => 4,
                    'title' => 'bbaacc',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
                [
                    'id' => 5,
                    'title' => 'aa4',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
                [
                    'id' => 6,
                    'title' => 'aa5',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => false,
                ],
            ]
        ]);
    }
}
