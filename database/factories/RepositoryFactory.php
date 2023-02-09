<?php

namespace Database\Factories;

use App\Enums\FormatEnum;
use App\Enums\ChannelEnum;
use App\Models\Repository;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Repository>
 */
class RepositoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Repository::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->colorName(),
            'organization_id' => Organization::factory()->create()->id,
            'channel' => ChannelEnum::WEB_HOOK,
            'format' => FormatEnum::CSV,
            'address' => $this->faker->url(),
            'command' => $this->faker->text()
        ];
    }
}
