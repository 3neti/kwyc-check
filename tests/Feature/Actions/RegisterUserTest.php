<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\OrganizationUser;
use App\Actions\RegisterUser;
use App\Models\Organization;
use App\Models\User;
use Tests\TestCase;


class RegisterUserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function register_user_action_accepts_organization_user_attributes_and_returns_user()
    {
        /*** arrange ***/
        $organization = Organization::factory()->create();
        $attributes = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'mobile' => $this->faker->e164PhoneNumber(),
            'password' => $this->faker->password(),
        ];

        /*** act ***/
        $user = RegisterUser::run($organization, $attributes);

        /*** assert ***/
        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);
        $this->assertEmpty(array_diff($attributes, $user->getAttributes()));
    }

    /** @test */
    public function register_user_action_end_point()
    {
        /*** arrange ***/
        $organization = Organization::factory()->create();
        $attributes = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'mobile' => $this->faker->e164PhoneNumber(),
            'password' => $this->faker->password(),
        ];

        /*** assert ***/
        $this->assertNull(User::where($attributes)->first());

        /*** act ***/
        $response = $this->postJson("/api/register-user/{$organization->id}", $attributes);

        /*** assert ***/
        $response->assertSuccessful();
        $user = User::where($attributes)->first();
        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);
    }
}
