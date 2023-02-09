<?php

namespace Tests\Unit;

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
}
