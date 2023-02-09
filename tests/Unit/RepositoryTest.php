<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Organization;
use App\Models\Repository;
use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;
use Tests\TestCase;

class RepositoryTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function repository_accepts_name_channel_format_address_command_and_associates_organization()
    {
        /*** arrange ***/
        $organization = Organization::factory()->create();
        $name = $this->faker->name;
        $channel = ChannelEnum::WEB_HOOK;
        $format = FormatEnum::CSV;
        $address = $this->faker->url();
        $command = $this->faker->text();

        /*** act ***/
        $repository = Repository::make(compact( 'name', 'channel', 'format', 'address', 'command'));
        $repository->organization()->associate($organization);
        $repository->save();

        /*** assert ***/
        $this->assertDatabaseHas(Repository::class, [
            'name' => $name,
            'channel' => $channel->value,
            'format' => $format->value,
            'address' => $address,
            'command' => $command,
            'organization_id' => $organization->id
        ]);
    }
}
