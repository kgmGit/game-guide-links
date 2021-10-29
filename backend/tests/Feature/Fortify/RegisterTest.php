<?php

namespace Tests\Feature\Fortify;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        Notification::fake();

        $body = [
            'name' => 'name',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->json('POST', 'register', $body);
        $response->assertStatus(201);

        $user = User::first();
        $this->assertNotNull($user);
        $this->assertEquals($body['name'], $user->name);
        $this->assertEquals($body['email'], $user->email);
        $this->assertTrue(Hash::check($body['password'], $user->password));

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test異常系_ユーザ名重複(): void
    {
        Notification::fake();

        $body_registered = [
            'name' => 'name',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $body = [
            'name' => 'name',
            'email' => 'test2@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $user = User::create($body_registered);

        $response = $this->json('POST', 'register', $body);
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

        $this->assertCount(1, User::all());

        Notification::assertNotSentTo($user, VerifyEmail::class);
    }

    public function test異常系_email重複(): void
    {
        Notification::fake();

        $body_registered = [
            'name' => 'name',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $body = [
            'name' => 'name2',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $user = User::create($body_registered);

        $response = $this->json('POST', 'register', $body);
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

        $this->assertCount(1, User::all());

        Notification::assertNotSentTo($user, VerifyEmail::class);
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
            (new CreateNewUser)->rules()
        );
        $this->assertEquals(
            $expect,
            $validator->passes()
        );
    }

    public function dataProviderValidation(): array
    {
        return [
            'OK_ユーザ最小、パスワード最小' => [
                [
                    'name' => 'a',
                    'email' => 'test@test.com',
                    'password' => '12345',
                    'password_confirmation' => '12345',
                ],
                true
            ],
            'OK_ユーザ名最大、メール最大、パスワード最大' => [
                [
                    'name' => Str::repeat('a', 20),
                    'email' => 'test@test.com' . Str::repeat('a', 255 - strlen('test@test.com')),
                    'password' => Str::repeat('a', 255),
                    'password_confirmation' => Str::repeat('a', 255),
                ],
                true
            ],
            'NG_ユーザ名未設定' => [
                [
                    'name' => '',
                    'email' => 'test@test.com',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                false
            ],
            'NG_ユーザ名最大超え' => [
                [
                    'name' => Str::repeat('a', 255),
                    'email' => 'test@test.com',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                false
            ],
            'NG_メール未設定' => [
                [
                    'name' => 'name',
                    'email' => '',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                false
            ],
            'NG_メール形式でない' => [
                [
                    'name' => 'name',
                    'email' => 'aaa',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                false
            ],
            'NG_メール最大超え' => [
                [
                    'name' => 'name',
                    'email' => Str::repeat('a', 256),
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                false
            ],
            'NG_パスワード未設定' => [
                [
                    'name' => 'name',
                    'email' => 'test@test.com',
                    'password' => '',
                    'password_confirmation' => '',
                ],
                false
            ],
            'NG_パスワード最小未満' => [
                [
                    'name' => 'name',
                    'email' => 'test@test.com',
                    'password' => '1234',
                    'password_confirmation' => '1234',
                ],
                false
            ],
            'NG_パスワード最大超え' => [
                [
                    'name' => 'name',
                    'email' => 'test@test.com',
                    'password' => Str::repeat('a', 256),
                    'password_confirmation' => Str::repeat('a', 256),
                ],
                false
            ],
            'NG_パスワード確認と異なる' => [
                [
                    'name' => 'name',
                    'email' => 'test@test.com',
                    'password' => 'password',
                    'password_confirmation' => 'passwordaaa',
                ],
                false
            ],
        ];
    }
}
