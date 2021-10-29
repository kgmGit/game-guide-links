<?php

namespace Tests\Feature\Fortify;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Auth::check());

        $response = $this->json('POST', 'logout');
        $response->assertStatus(204);

        $this->assertFalse(Auth::check());
    }
}
