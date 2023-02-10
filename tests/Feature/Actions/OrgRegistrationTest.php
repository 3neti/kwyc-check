<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Actions\OrgRegistration;
use App\Models\Organization;
use App\Models\Repository;
use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;
use App\Models\Campaign;
use App\Models\Package;
use App\Models\User;
use Tests\TestCase;

class OrgRegistrationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function org_registration_accepts_org_name_channel_format_address_command_package_user_and_returns_campaign()
    {
        /*** arrange ***/
        $orgName = $this->faker->company();
        $channelEnum = ChannelEnum::random();
        $formatEnum = FormatEnum::random();
        $address = $this->faker->url();
        $command = $this->faker->text(20);
        $package = Package::factory()->create();
        $user = User::factory()->create();

        /*** act ***/
        $campaign = OrgRegistration::run($user, $orgName, $channelEnum, $formatEnum, $address, $command, $package);

        /*** assert ***/
        $this->assertInstanceOf(Campaign::class, $campaign);
        $this->assertInstanceOf(Repository::class, $campaign->repository);
        $this->assertInstanceOf(Organization::class, $campaign->repository->organization);
        $this->assertTrue($campaign->package->is($package));
        $this->assertSame($channelEnum, $campaign->repository->channel);
        $this->assertSame($formatEnum, $campaign->repository->format);
        $this->assertSame($address, $campaign->repository->address);
        $this->assertSame($command, $campaign->repository->command);
        $this->assertSame($orgName, $campaign->repository->organization->name);
    }

    /** @test */
    public function org_registration_end_point()
    {
        /*** arrange ***/
        $orgName = $this->faker->company();
        $channelEnum = ChannelEnum::random();
        $formatEnum = FormatEnum::random();
        $address = $this->faker->url();
        $command = $this->faker->text(20);
        $package = Package::factory()->create();
        $user = User::factory()->create();

        /*** act ***/
        $response = $this->postJson("/api/register-organization", [
            'name' => $orgName,
            'channel' => $channelEnum->value,
            'format' => $formatEnum->value,
            'address' => $address,
            'command' => $command,
            'package' => $package->code
        ], [
            'Authorization' => 'Bearer ' . $user->createToken('mobile')->plainTextToken
        ]);

        /*** assert ***/
        $response->assertSuccessful();
        tap(Campaign::find($response->json('id')), function(Campaign $campaign)
        use ($response, $package, $orgName, $channelEnum, $formatEnum, $address, $command, $user) {
            $this->assertSame($orgName, $campaign->repository->organization->name);
            $this->assertSame($channelEnum, $campaign->repository->channel);
            $this->assertSame($formatEnum, $campaign->repository->format);
            $this->assertSame($address, $campaign->repository->address);
            $this->assertSame($command, $campaign->repository->command);
            $this->assertSame($package->code, $campaign->package->code);
            $this->assertTrue($user->is($campaign->repository->organization->admin));
        });
    }
}
