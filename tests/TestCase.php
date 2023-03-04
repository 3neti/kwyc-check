<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = $this->makeFaker('en_PH');
    }

    protected function fakeUserAttributes(array $attribs = []): array
    {
        return array_merge([
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'mobile' => $this->faker->mobileNumber(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true],
            $attribs);
    }

    protected function fakeCampaignAttributes(array $attribs = []): array
    {
        return array_merge([
            'name' => $this->faker->company(),
            'channel' => ChannelEnum::random(),
            'format' => FormatEnum::random(),
            'address' => $this->faker->email(),
            'command' => $this->faker->sentence(),
            'package' =>  Arr::random(['registration', 'inspection', 'qualification', 'redemption'])],
            $attribs);
    }
}
