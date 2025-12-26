<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = [
            'Raipur',
            'Bhilai',
            'Bilaspur',
            'Korba',
            'Durg',
            'Rajnandgaon',
            'Raigarh',
            'Jagdalpur',
            'Ambikapur',
            'Dhamtari',
            'Chirmiri',
            'Bhatapara',
            'Mahasamund',
            'Dalli-Rajhara',
            'Kanker',
            'Kawardha',
            'Champa',
            'Naila Janjgir',
            'Dongargarh',
            'Tilda Newra',
            'Mungeli',
            'Manendragarh',
            'Kondagaon',
            'Gobranawapara',
            'Bemetara',
            'Baikunthpur',
            'Balod',
            'Jashpur Nagar',
            'Kharsia',
            'Kirandul',
            'Sakti',
            'Gariaband',
            'Surajpur',
            'Baloda Bazar',
            'Kumhari',
            'Jamul',
            'Dipka',
            'Katghora',
            'Sarangarh',
            'Dongar Parasia',
            'Pendra',
            'Bilha',
            'Arang',
            'Simga',
            'Jhagrakhand',
            'Chhuikhadan',
            'Kunkuri',
            'Pathalgaon',
            'Lormi',
            'Takhatpur',
            'Akaltara',
            'Khairagarh',
            'Bhanpuri',
            'Kharod',
            'Ratanpur',
            'Gandai',
            'Kurud',
            'Ahiwara',
            'Bhaisma',
            'Deori',
            'Dharamjaigarh',
            'Gaurella',
            'Gidam',
            'Kirodimalnagar',
            'Kotma',
            'Pasan',
            'Pipariya',
            'Sargaon',
            'Shivrinarayan',
            'Sirgiti',
            'Sitapur',
            'Telgaon',
            'Tumgaon',
            'Urla',
            'Vishrampur'
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(['name' => $city]);
        }
    }
}
