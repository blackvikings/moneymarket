<?php

namespace Database\Factories;

use App\Models\VehicleManufacture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleName>
 */
class VehicleNameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_manufacture_id' => VehicleManufacture::factory(),
            'name' => $this->faker->word . ' ' . $this->faker->randomDigit,
        ];
    }
}
