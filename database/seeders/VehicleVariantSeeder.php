<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleManufacture;
use App\Models\VehicleName;

class VehicleVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Maruti Suzuki' => [
                'Swift' => ['LXI', 'VXI', 'ZXI', 'ZXI+'],
                'Baleno' => ['Sigma', 'Delta', 'Zeta', 'Alpha'],
                'Brezza' => ['LXI', 'VXI', 'ZXI', 'ZXI+'],
                'Ertiga' => ['LXI', 'VXI', 'ZXI', 'ZXI+'],
                'Alto K10' => ['Std', 'LXI', 'VXI', 'VXI+'],
            ],
            'Hyundai' => [
                'Creta' => ['E', 'EX', 'S', 'SX', 'SX(O)'],
                'Venue' => ['E', 'S', 'S+', 'SX', 'SX(O)'],
                'i20' => ['Magna', 'Sportz', 'Asta', 'Asta(O)'],
                'Verna' => ['EX', 'S', 'SX', 'SX(O)'],
            ],
            'Tata' => [
                'Nexon' => ['XE', 'XM', 'XZ', 'XZ+', 'XZ+(O)'],
                'Punch' => ['Pure', 'Adventure', 'Accomplished', 'Creative'],
                'Harrier' => ['XE', 'XM', 'XT', 'XZ', 'XZ+'],
                'Tiago' => ['XE', 'XT', 'XZ', 'XZ+'],
            ],
            'Mahindra' => [
                'Thar' => ['AX', 'AX(O)', 'LX'],
                'XUV700' => ['MX', 'AX3', 'AX5', 'AX7', 'AX7 L'],
                'Scorpio N' => ['Z2', 'Z4', 'Z6', 'Z8', 'Z8 L'],
                'Bolero' => ['B4', 'B6', 'B6(O)'],
            ],
            'Kia' => [
                'Seltos' => ['HTE', 'HTK', 'HTK+', 'HTX', 'HTX+', 'GTX+'],
                'Sonet' => ['HTE', 'HTK', 'HTK+', 'HTX', 'HTX+', 'GTX+'],
                'Carens' => ['Premium', 'Prestige', 'Prestige Plus', 'Luxury', 'Luxury Plus'],
            ],
            'Toyota' => [
                'Fortuner' => ['4x2 MT', '4x2 AT', '4x4 MT', '4x4 AT', 'Legender'],
                'Innova Crysta' => ['GX', 'VX', 'ZX'],
                'Glanza' => ['E', 'S', 'G', 'V'],
            ],
            'Honda' => [
                'City' => ['V', 'VX', 'ZX'],
                'Amaze' => ['E', 'S', 'VX'],
            ],
        ];

        foreach ($data as $mfgName => $models) {
            $manufacture = VehicleManufacture::firstOrCreate(['name' => $mfgName]);

            foreach ($models as $modelName => $variants) {
                foreach ($variants as $variant) {
                    VehicleName::firstOrCreate([
                        'vehicle_manufacture_id' => $manufacture->id,
                        'name' => $modelName,
                        'variant' => $variant,
                    ]);
                }
            }
        }
    }
}
