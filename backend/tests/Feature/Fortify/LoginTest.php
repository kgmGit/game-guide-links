<?php

namespace Tests\Feature\Fortify;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系()
    {
        $body = [
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        User::factory()->create([
            'email' => $body['email'],
            'password' => Hash::make($body['password']),
        ]);

        $this->assertFalse(Auth::check());

        $response = $this->json('POST', 'login', $body);
        $response->assertStatus(200);

        $this->assertTrue(Auth::check());
    }

    public function test異常系_メールアドレスが異なる()
    {
        $body = [
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        User::factory()->create([
            'email' => $body['email'],
            'password' => Hash::make($body['password']),
        ]);

        $this->assertFalse(Auth::check());

        $body['email'] = 'wrong_email';

        $response = $this->json('POST', 'login', $body);
        $response->assertStatus(422);

        $this->assertFalse(Auth::check());
    }

    public function test異常系_パスワードが異なる()
    {
        $body = [
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        User::factory()->create([
            'email' => $body['email'],
            'password' => Hash::make($body['password']),
        ]);

        $this->assertFalse(Auth::check());

        $body['password'] = 'wrong_password';

        $response = $this->json('POST', 'login', $body);
        $response->assertStatus(422);

        $this->assertFalse(Auth::check());
    }
}
