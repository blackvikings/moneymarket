<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use App\Models\Bank;
use App\Models\BankBranch;
use App\Models\Firm;
use App\Models\VehicleManufacture;
use App\Models\VehicleName;
use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition()
    {
        $leadType = $this->faker->randomElement(['Loan', 'Insurance', 'RTO']);
        $status = $this->faker->randomElement(['Lead', 'Bank Assign', 'Rejected', 'Not_Active']);
        $crmStage = null;
        if ($leadType == 'Loan' && $status == 'Bank Assign') {
            $crmStage = $this->faker->randomElement(['New', 'Login', 'FI Document', 'Vehicle Evaluation', 'Credit', 'Approved', 'Agreement', 'Disbursal']);
        }

        return [
            'lead_type' => $leadType,
            'customer_name' => $this->faker->name,
            'father_name' => $this->faker->name('male'),
            'contact_number' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'residence_type' => $this->faker->randomElement(['Owned', 'Rented']),
            'current_address_line' => $this->faker->address,
            'current_address_city' => City::inRandomOrder()->first()->id ?? City::factory(),
            'current_address_state' => State::inRandomOrder()->first()->id ?? State::factory(),
            'current_address_pincode' => $this->faker->postcode,
            'permanent_address_line' => $this->faker->address,
            'permanent_address_city' => City::inRandomOrder()->first()->id ?? City::factory(),
            'permanent_address_state' => State::inRandomOrder()->first()->id ?? State::factory(),
            'permanent_address_pincode' => $this->faker->postcode,
            'vehicle_purchase_type' => $this->faker->randomElement(['New Purchase', 'Old Purchase']),
            'manufacture_id' => VehicleManufacture::inRandomOrder()->first()->id ?? VehicleManufacture::factory(),
            'vehicle_name_id' => VehicleName::inRandomOrder()->first()->id ?? VehicleName::factory(),
            'vehicle_variant' => $this->faker->word,
            'mfg_year' => $this->faker->year, // Changed from manufacturing_year to mfg_year
            'rc_number' => $this->faker->bothify('??##??####'),
            'engine_number' => $this->faker->bothify('??#####'),
            'chassis_number' => $this->faker->bothify('??#####'),
            'status' => $status,
            'crm_stage' => $crmStage,
            'assigned_to' => User::inRandomOrder()->first()->id ?? User::factory(),
            'created_by' => User::inRandomOrder()->first()->id ?? User::factory(),
            'firm_id' => Firm::inRandomOrder()->first()->id ?? Firm::factory(),
            'bank_id' => Bank::inRandomOrder()->first()->id ?? Bank::factory(),
            'bank_branch_id' => BankBranch::inRandomOrder()->first()->id ?? BankBranch::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
