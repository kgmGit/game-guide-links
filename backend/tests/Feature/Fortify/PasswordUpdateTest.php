<?php

namespace Tests\Feature\Fortify;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        $body = [
            'current_password' => 'password',
            'password' => 'change_password',
            'password_confirmation' => 'change_password'
        ];

        /** @var User $user */
        $user = User::factory()->create(['password' => Hash::make($body['current_password'])]);

        $this->actingAs($user);
        $response = $this->json('PUT', 'user/password', $body);
        $response->assertStatus(200);

        $user = DB::table('users')->first();
        $this->assertTrue(Hash::check($body['password'], $user->password));
    }

    public function test異常系_現在のパスワードが異なる(): void
    {
        $body = [
            'current_password' => 'wrong_password',
            'password' => 'change_password',
            'password_confirmation' => 'change_password'
        ];

        /** @var User $user */
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $this->actingAs($user);
        $response = $this->json('PUT', 'user/password', $body);

        $response
            ->assertStatus(422)
            ->assertJson(
                [
                    'errors' => [
                        'current_password' => [
                            '入力されたパスワードが現在のパスワードと一致しません',
                        ],
                    ],
                ]
            );

        $user = DB::table('users')->first();
        $this->assertFalse(Hash::check($body['password'], $user->password));
    }
}
