<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleManufacture;
use App\Models\VehicleName;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        VehicleManufacture::truncate();
        VehicleName::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $vehicles = [
            'Maruti Suzuki' => [
                ['name' => 'Swift', 'variant' => 'VXI'],
                ['name' => 'Swift', 'variant' => 'ZXI'],
                ['name' => 'Baleno', 'variant' => 'Sigma'],
                ['name' => 'Baleno', 'variant' => 'Delta'],
                ['name' => 'Alto 800', 'variant' => 'LXI'],
                ['name' => 'Wagon R', 'variant' => 'VXI'],
                ['name' => 'Dzire', 'variant' => 'VXI'],
            ],
            'Hyundai' => [
                ['name' => 'i20', 'variant' => 'Magna'],
                ['name' => 'i20', 'variant' => 'Sportz'],
                ['name' => 'Creta', 'variant' => 'E'],
                ['name' => 'Creta', 'variant' => 'EX'],
                ['name' => 'Verna', 'variant' => 'S'],
                ['name' => 'Venue', 'variant' => 'S'],
            ],
            'Tata' => [
                ['name' => 'Nexon', 'variant' => 'XE'],
                ['name' => 'Nexon', 'variant' => 'XM'],
                ['name' => 'Punch', 'variant' => 'Pure'],
                ['name' => 'Altroz', 'variant' => 'XE'],
                ['name' => 'Harrier', 'variant' => 'XE'],
            ],
            'Mahindra' => [
                ['name' => 'Thar', 'variant' => 'AX'],
                ['name' => 'Thar', 'variant' => 'LX'],
                ['name' => 'XUV700', 'variant' => 'MX'],
                ['name' => 'XUV700', 'variant' => 'AX3'],
                ['name' => 'Scorpio-N', 'variant' => 'Z2'],
                ['name' => 'Scorpio-N', 'variant' => 'Z4'],
            ],
            'Honda' => [
                ['name' => 'City', 'variant' => 'V'],
                ['name' => 'City', 'variant' => 'VX'],
                ['name' => 'Amaze', 'variant' => 'S'],
                ['name' => 'Amaze', 'variant' => 'VX'],
            ],
            'Toyota' => [
                ['name' => 'Fortuner', 'variant' => '4x2'],
                ['name' => 'Innova Crysta', 'variant' => 'GX'],
                ['name' => 'Glanza', 'variant' => 'G'],
            ],
            'Kia' => [
                ['name' => 'Seltos', 'variant' => 'HTE'],
                ['name' => 'Seltos', 'variant' => 'HTK'],
                ['name' => 'Sonet', 'variant' => 'HTE'],
            ],
        ];

        foreach ($vehicles as $manufacturerName => $models) {
            $manufacturer = VehicleManufacture::create(['name' => $manufacturerName]);

            foreach ($models as $model) {
                $manufacturer->vehicleNames()->create([
                    'name' => $model['name'],
                    'variant' => $model['variant']
                ]);
            }
        }
    }
}
