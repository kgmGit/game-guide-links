<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        $this->artisan('create:admin-user', [
            'name' => 'user_name',
            'email' => 'user@user.com',
            'password' => 'password'
        ])->assertExitCode(0);

        $user = User::first();
        $this->assertEquals('user_name', $user->name);
        $this->assertEquals('user@user.com', $user->email);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertEquals(true, $user->admin);
    }

    public function test異常系_バリデーションエラー(): void
    {
        $this->artisan('create:admin-user', [
            'name' => '',
            'email' => 'user@user.com',
            'password' => 'password'
            ])->assertExitCode(1);

        $user = User::first();
        $this->assertNull($user);
    }
}
