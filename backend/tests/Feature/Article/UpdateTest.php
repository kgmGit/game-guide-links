<?php

namespace Tests\Feature\Article;

use App\Http\Requests\Article\UpdateRequest;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_作成したサイト(): void
    {
        $this->withoutExceptionHandling();
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        Article::factory()->for($user)->for($game)->create([
            'title' => 'article_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ]);

        $body = [
            'title' => 'update_title',
            'outline' => '更新概要',
            'content' => '<p>更新コンテンツ</p>'
        ];

        $this->actingAs($user);
        $response = $this->json('PATCH', 'api/games/game_title/articles/1', $body);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => $body['title'],
                    'outline' => $body['outline'],
                    'content' => $body['content'],
                    'owner' => true,
                    'owner_name' => $user->name,
                    'game_title' => 'game_title'
                ]
            ]);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($body['title'], $article->title);
        $this->assertEquals($body['outline'], $article->outline);
        $this->assertEquals($body['content'], $article->content);
    }

    public function test正常系_管理者(): void
    {
        /** @var User $adminUser */
        $adminUser = User::factory()->admin()->create();

        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        Article::factory()->for($user)->for($game)->create([
            'title' => 'article_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ]);

        $body = [
            'title' => 'update_title',
            'outline' => '更新概要',
            'content' => '<p>更新コンテンツ</p>'
        ];

        $this->actingAs($adminUser);
        $response = $this->json('PATCH', 'api/games/game_title/articles/1', $body);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => $body['title'],
                    'outline' => $body['outline'],
                    'content' => $body['content'],
                    'owner' => false,
                    'owner_name' => $user->name,
                    'game_title' => 'game_title',
                ]
            ]);

        /** @var Article $article */
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
        $game = $user->games()->create(['title' => 'game_title']);
        $preBody = [
            'title' => 'article_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];
        Article::factory()->for($user)->for($game)->create($preBody);
        $body = [
            'title' => 'update_title',
            'outline' => '更新概要',
            'content' => '<p>更新コンテンツ</p>'
        ];

        $this->actingAs($user);
        $response = $this->json('PATCH', 'api/games/not_exists_game_title/articles/1', $body);

        $response->assertStatus(404);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($preBody['title'], $article->title);
        $this->assertEquals($preBody['outline'], $article->outline);
        $this->assertEquals($preBody['content'], $article->content);
    }

    public function test異常系_紐付いているゲームでない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $preBody = [
            'title' => 'article_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];
        Article::factory()->for($user)->for($game)->create($preBody);
        $body = [
            'title' => 'update_title',
            'outline' => '更新概要',
            'content' => '<p>更新コンテンツ</p>'
        ];

        $user->games()->create(['title' => 'not_link_game_title']);

        $this->actingAs($user);
        $response = $this->json('PATCH', 'api/games/not_link_game_title/articles/1', $body);

        $response->assertStatus(404);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($preBody['title'], $article->title);
        $this->assertEquals($preBody['outline'], $article->outline);
        $this->assertEquals($preBody['content'], $article->content);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $preBody = [
            'title' => 'article_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];
        Article::factory()->for($user)->for($game)->create($preBody);
        $body = [
            'title' => 'update_title',
            'outline' => '更新概要',
            'content' => '<p>更新コンテンツ</p>'
        ];

        $response = $this->json('PATCH', 'api/games/game_title/articles/1', $body);

        $response->assertStatus(401);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($preBody['title'], $article->title);
        $this->assertEquals($preBody['outline'], $article->outline);
        $this->assertEquals($preBody['content'], $article->content);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $preBody = [
            'title' => 'article_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];
        Article::factory()->for($user)->for($game)->create($preBody);
        $body = [
            'title' => 'update_title',
            'outline' => '更新概要',
            'content' => '<p>更新コンテンツ</p>'
        ];

        $this->actingAs($user);
        $response = $this->json('PATCH', 'api/games/game_title/articles/1', $body);

        $response->assertStatus(403);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($preBody['title'], $article->title);
        $this->assertEquals($preBody['outline'], $article->outline);
        $this->assertEquals($preBody['content'], $article->content);
    }

    public function test異常系_作成したユーザでない(): void
    {
        /** @var User $notCreateArticleUser */
        $notCreateArticleUser = User::factory()->create();

        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $preBody = [
            'title' => 'article_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];
        Article::factory()->for($user)->for($game)->create($preBody);
        $body = [
            'title' => 'update_title',
            'outline' => '更新概要',
            'content' => '<p>更新コンテンツ</p>'
        ];

        $this->actingAs($notCreateArticleUser);
        $response = $this->json('PATCH', 'api/games/game_title/articles/1', $body);

        $response->assertStatus(403);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($preBody['title'], $article->title);
        $this->assertEquals($preBody['outline'], $article->outline);
        $this->assertEquals($preBody['content'], $article->content);
    }

    public function test異常系_バリデーションエラー(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $preBody = [
            'title' => 'article_title',
            'outline' => '概要',
            'content' => '<p>コンテンツ</p>'
        ];
        Article::factory()->for($user)->for($game)->create($preBody);
        $body = [
            'title' => '',
            'outline' => '更新概要',
            'content' => '<p>更新コンテンツ</p>'
        ];

        $this->actingAs($user);
        $response = $this->json('PATCH', 'api/games/game_title/articles/1', $body);

        $response->assertStatus(422);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($preBody['title'], $article->title);
        $this->assertEquals($preBody['outline'], $article->outline);
        $this->assertEquals($preBody['content'], $article->content);
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
            (new UpdateRequest())->rules()
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
