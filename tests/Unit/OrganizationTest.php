<?php

namespace Tests\Unit;

use App\Models\OrganizationUser;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Database\QueryException;
use Database\Seeders\UserSeeder;
use App\Models\Organization;
use App\Models\User;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function organization_requires_name()
    {
        /*** assert ***/
        $this->expectException(QueryException::class);

        /*** arrange ***/
        $admin = User::factory()->create();

        /*** act ***/
        $organization = Organization::make([]);
        $organization->admin()->associate($admin);
        $organization->save();
    }

    /** @test */
    public function organization_requires_admin()
    {
        /*** assert ***/
        $this->expectException(QueryException::class);

        /*** arrange ***/
        $company = $this->faker->company();

        /*** act ***/
        $organization = Organization::make([]);
        $organization->name = $company;
        $organization->save();
    }

    /** @test */
    public function organization_accepts_name_and_associated_admin()
    {
        /*** arrange ***/
        $company = $this->faker->company();
        $admin = User::factory()->create();

        /*** act ***/
        $organization = Organization::make([]);
        $organization->name = $company;
        $organization->admin()->associate($admin);
        $organization->save();

        /*** assert ***/
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'admin_id' => $admin->id,
        ]);
        $this->assertTrue($admin->is($organization->admin));
    }

    /** @test */
    public function organization_can_have_many_users_with_default_active()
    {
        /*** assert ***/
        $organization = Organization::factory()->create();
        [$user1, $user2, $user3] = User::factory(3)->create();

        /*** arrange ***/
        $organization->users()->attach($user1, ['active' => false]);
        $organization->users()->attach($user2);

        /*** act ***/
        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $user1->id,
            'active' => false
        ]);
        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $user2->id,
            'active' => true
        ]);
        $this->assertDatabaseMissing(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $user3->id,
        ]);
    }

    /** @test */
    public function organization_must_have_unique_users()
    {
        /*** assert ***/
        $this->expectExceptionCode(23000);

        /*** arrange ***/
        $organization = Organization::factory()->create();
        [$user1, $user2] = User::factory(2)->create();
        $organization->users()->attach($user1);
        $organization->users()->attach($user2);

        /*** act ***/
        $organization->users()->attach($user1);
    }

    /** @test */
    public function organization_admin_is_automatically_included_in_the_organization()
    {
        /*** arrange ***/
        $admin = User::factory()->create();

        /*** act ***/
        $organization = Organization::factory()->create(['admin_id' => $admin->id]);

        /*** assert ***/
        $this->assertTrue($organization->admin->is($admin));
        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $admin->id,
        ]);
    }

    /** @test */
    public function organization_has_many_repositories()
    {
        /*** arrange ***/
        $organization = Organization::factory()->create();

        /*** act ***/
        Repository::factory(3)->create(['organization_id' => $organization->id]);

        /*** assert ***/
        $this->assertCount(3, $organization->repositories);
    }
}
