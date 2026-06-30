<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_customer_can_register(): void
    {
        Event::fake([Registered::class]);

        $response = $this->postJson('/api/register', [
            'name' => 'Joe Doe',
            'email' => 'joe.doe@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.email', 'joe.doe@example.test')
            ->assertJsonPath('data.roles', [Role::Customer->value]);

        $user = User::query()
            ->where('email', 'joe.doe@example.test')
            ->firstOrFail();

        $this->assertTrue($user->hasRole(Role::Customer->value));

        Event::assertDispatched(Registered::class);
    }

    public function test_registration_requires_valid_payload(): void
    {
        $this->postJson('/api/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);

        $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'taken@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
