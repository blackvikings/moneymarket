<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            VehicleSeeder::class,
        ]);

        \App\Models\Bank::factory(10)->hasBranches(3)->create();
        \App\Models\Firm::factory(10)->create();
        \App\Models\RtoAgent::factory(10)->has(\App\Models\RtoAgentCity::factory()->count(3), 'cities')->create();
        \App\Models\Insurance::factory(10)->has(\App\Models\InsuranceType::factory()->has(\App\Models\InsuranceSubType::factory()->count(2), 'subTypes')->count(2), 'types')->create();
        \App\Models\Lead::factory(20)->create();
    }
}
