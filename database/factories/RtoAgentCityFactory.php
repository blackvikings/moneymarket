<?php

namespace Database\Factories;

use App\Models\RtoAgent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RtoAgentCity>
 */
class RtoAgentCityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rto_agent_id' => RtoAgent::factory(),
            'city_name' => $this->faker->city,
        ];
    }
}
