<?php

namespace Tests\Feature\Article;

use App\Http\Requests\Article\StoreRequest;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => 'site_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/articles', $body);

        $response->assertStatus(201)
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'title' => $body['title'],
                    'outline' => $body['outline'],
                    'content' => $body['content'],
                    'favorites_count' => 0,
                    'favorited' => false,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => true,
                    'owner_name' => $user->name,
                    'game_title' => 'game_title'
                ]
            ]);

        /** @var Article $site */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($body['title'], $article->title);
        $this->assertEquals($body['outline'], $article->outline);
        $this->assertEquals($body['content'], $article->content);
    }

    public function test異常系_ゲームが存在しない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => 'site_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/not_exists_game_title/articles', $body);

        $response->assertStatus(404);

        $this->assertNull(Article::query()->first());
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => 'site_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];

        $response = $this->json('POST', 'api/games/game_title/articles', $body);

        $response->assertStatus(401);

        $this->assertNull(Article::query()->first());
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => 'site_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/articles', $body);

        $response->assertStatus(403);

        $this->assertNull(Article::query()->first());
    }

    public function test異常系_バリデーションエラー(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => '',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/articles', $body);

        $response->assertStatus(422);

        $this->assertNull(Article::query()->first());
    }

    /**
     * @param array $data
     * @param bool $expect
     * @dataProvider dataProviderValidation
     */
    public function testバリデーション(array $data, bool $expect): void
    {
        $validator = Validator::make(
            $data,
            (new StoreRequest())->rules()
        );
        $this->assertEquals(
            $expect,
            $validator->passes()
        );
    }

    public function dataProviderValidation(): array
    {
        return [
            'OK_タイトル最小' => [
                [
                    'title' => 'a',
                    'outline' => '概要',
                    'content' => '<p>コンテンツ</p>',
                ],
                true
            ],
            'OK_タイトル最大' => [
                [
                    'title' => Str::repeat('a', 20),
                    'outline' => '概要',
                    'content' => '<p>コンテンツ</p>',
                ],
                true
            ],
            'OK_概要最小' => [
                [
                    'title' => 'title',
                    'outline' => 'a',
                    'content' => '<p>コンテンツ</p>',
                ],
                true
            ],
            'OK_概要最大' => [
                [
                    'title' => 'title',
                    'outline' => Str::repeat('a', 200),
                    'content' => '<p>コンテンツ</p>',
                ],
                true
            ],
            'OK_内容最小' => [
                [
                    'title' => 'title',
                    'outline' => '概要',
                    'content' => 'a',
                ],
                true
            ],
            'OK_内容最大' => [
                [
                    'title' => 'title',
                    'outline' => '概要',
                    'content' => Str::repeat('あ', 2000), // 3byte * 2000 = 6000byte
                ],
                true
            ],
            'NG_タイトル最小未満' => [
                [
                    'title' => '',
                    'outline' => '概要',
                    'content' => '<p>コンテンツ</p>',
                ],
                false
            ],
            'NG_タイトル最大超え' => [
                [
                    'title' => Str::repeat('a', 21),
                    'outline' => '概要',
                    'content' => '<p>コンテンツ</p>',
                ],
                false
            ],
            'NG_概要最小未満' => [
                [
                    'title' => 'title',
                    'outline' => '',
                    'content' => '<p>コンテンツ</p>',
                ],
                false
            ],
            'NG_概要最大超え' => [
                [
                    'title' => 'title',
                    'outline' => Str::repeat('a', 201),
                    'content' => '<p>コンテンツ</p>',
                ],
                false
            ],
            'NG_内容最小未満' => [
                [
                    'title' => 'title',
                    'outline' => '概要',
                    'content' => '',
                ],
                false
            ],
            'NG_内容最大超え' => [
                [
                    'title' => 'title',
                    'outline' => '概要',
                    'content' => Str::repeat('あ', 2000) . 'a',
                ],
                false
            ],
        ];
    }
}
