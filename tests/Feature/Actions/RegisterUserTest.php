<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\OrganizationUser;
use App\Actions\RegisterUser;
use App\Models\Organization;
use Illuminate\Support\Arr;
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
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        $attributes = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'mobile' => $mobile,
            'password' => $this->faker->password(),
        ];

        /*** act ***/
        $user = RegisterUser::run($organization, $attributes);

        /*** assert ***/
        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);
        Arr::set($attributes, 'mobile', phone($mobile, 'PH')->formatE164());
        $this->assertEmpty(array_diff($attributes, $user->getAttributes()));
    }

    /** @test */
    public function register_user_action_end_point()
    {
        /*** arrange ***/
        $organization = Organization::factory()->create();
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        $attributes = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'mobile' => $mobile,
            'password' => $this->faker->password(),
        ];

        /*** assert ***/
        $this->assertNull(User::where($attributes)->first());

        /*** act ***/
        $response = $this->postJson("/api/register-user/{$organization->id}", $attributes);

        /*** assert ***/
        $response->assertSuccessful();
        $user = User::fromMobile($mobile);
        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);
    }
}
