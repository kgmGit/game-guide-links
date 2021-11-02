<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'user');

        $response->assertStatus(204);

        $this->assertGuest('web');

        $this->assertNull(User::first());
    }
}
