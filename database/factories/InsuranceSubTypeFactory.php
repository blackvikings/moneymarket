<?php

namespace Database\Factories;

use App\Models\InsuranceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsuranceSubType>
 */
class InsuranceSubTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'insurance_type_id' => InsuranceType::factory(),
            'name' => $this->faker->word . ' Plan',
        ];
    }
}
