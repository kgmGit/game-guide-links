<?php

namespace Tests\Feature\Fortify;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'name',
            'email' => 'test@test.com'
        ]);

        $body = [
            'name' => 'change_name',
            'email' => 'change@test.com',
        ];

        $this->actingAs($user);
        $response = $this->json('PUT', 'user/profile-information', $body);
        $response->assertStatus(200);

        $user = User::first();
        $this->assertNotNull($user);
        $this->assertEquals($body['name'], $user->name);
        $this->assertEquals($body['email'], $user->email);
    }

    public function test異常系_ユーザ名重複(): void
    {
        $body_registered = [
            'name' => 'registered_name',
            'email' => 'registered@test.com',
        ];

        User::factory()->create($body_registered);
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'name',
            'email' => 'test@test.com'
        ]);

        $body = [
            'name' => $body_registered['name'],
            'email' => 'change@test.com',
        ];

        $this->actingAs($user);
        $response = $this->json('PUT', 'user/profile-information', $body);
        $response
            ->assertStatus(422)
            ->assertJson(
                [
                    'errors' => [
                        'name' => [
                            'すでに登録されています',
                        ],
                    ],
                ]
            );

        $user = User::find(2);
        $this->assertNotNull($user);
        $this->assertNotEquals($body['name'], $user->name);
        $this->assertNotEquals($body['email'], $user->email);
    }

    public function test異常系_email重複(): void
    {
        $body_registered = [
            'name' => 'registered_name',
            'email' => 'registered@test.com',
        ];

        User::factory()->create($body_registered);
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'name',
            'email' => 'test@test.com'
        ]);

        $body = [
            'name' => 'change_name',
            'email' => $body_registered['email'],
        ];

        $this->actingAs($user);
        $response = $this->json('PUT', 'user/profile-information', $body);
        $response
            ->assertStatus(422)
            ->assertJson(
                [
                    'errors' => [
                        'email' => [
                            'すでに登録されています',
                        ],
                    ],
                ]
            );

        $user = User::find(2);
        $this->assertNotNull($user);
        $this->assertNotEquals($body['name'], $user->name);
        $this->assertNotEquals($body['email'], $user->email);
    }

    /**
     * @param array $data
     * @param bool $expect
     * @dataProvider dataProviderValidation
     */
    public function testバリデーション(array $data, bool $expect): void
    {
        $user = User::factory()->make([
            'name' => 'name',
            'email' => 'test@test.com'
        ]);
        $validator = Validator::make(
            $data,
            (new UpdateUserProfileInformation)->rules($user)
        );
        $this->assertEquals(
            $expect,
            $validator->passes()
        );
    }

    public function dataProviderValidation(): array
    {
        return [
            'OK_ユーザ最小' => [
                [
                    'name' => 'a',
                    'email' => 'test@test.com',
                ],
                true
            ],
            'OK_ユーザ名最大、メール最大' => [
                [
                    'name' => Str::repeat('a', 20),
                    'email' => 'test@test.com' . Str::repeat('a', 255 - strlen('test@test.com')),
                ],
                true
            ],
            'NG_ユーザ名未設定' => [
                [
                    'name' => '',
                    'email' => 'test@test.com',
                ],
                false
            ],
            'NG_ユーザ名最大超え' => [
                [
                    'name' => Str::repeat('a', 21),
                    'email' => 'test@test.com',
                ],
                false
            ],
            'NG_メール未設定' => [
                [
                    'name' => 'name',
                    'email' => '',
                ],
                false
            ],
            'NG_メール形式でない' => [
                [
                    'name' => 'name',
                    'email' => 'aaa',
                ],
                false
            ],
            'NG_メール最大超え' => [
                [
                    'name' => 'name',
                    'email' => Str::repeat('a', 256),
                ],
                false
            ],
        ];
    }
}
