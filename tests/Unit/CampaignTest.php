<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Repository;
use App\Models\Campaign;
use App\Models\Package;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function campaign_accepts_active_start_date_end_date_associates_package_and_repository()
    {
        /*** arrange ***/
        $active = $this->faker->boolean;
        $start_date = $this->faker->date();
        $end_date = $this->faker->date();
        $package = Package::factory()->create();
        $repository = Repository::factory()->create();

        /*** act ***/
        $campaign = Campaign::make(compact('active', 'start_date', 'end_date'));
        $campaign->package()->associate($package);
        $campaign->repository()->associate($repository);
        $campaign->save();

        /*** assert ***/
        $this->assertDatabaseHas(Campaign::class, [
            'package_id' => $package->id,
            'repository_id' => $repository->id,
            'active' => $active,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }
}
