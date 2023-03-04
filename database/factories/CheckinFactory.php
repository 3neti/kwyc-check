<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Checkin;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Checkin>
 */
class CheckinFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Checkin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $campaign = Campaign::factory()->create();
        $contact = Contact::factory()->create();
        return [
            'uuid' => $this->faker->uuid(),
            'campaign_id' => $campaign->id,
            'agent_id' => $campaign->repository->organization->admin->id,
            'person_id' => $contact->id,
            'person_type' => Contact::class,
            'url' => $this->faker->url(),
            'data' => $this->faker->rgbColorAsArray(),
            'longitude' => $this->faker->longitude(),
            'latitude' => $this->faker->latitude()
        ];
    }
}
