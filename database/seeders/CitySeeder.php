<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\State;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chhattisgarh = State::where('name', 'Chhattisgarh')->first();

        if ($chhattisgarh) {
            $cities = [
                ['name' => 'Raipur', 'pincode' => '492001'],
                ['name' => 'Bhilai', 'pincode' => '490001'],
                ['name' => 'Bilaspur', 'pincode' => '495001'],
                ['name' => 'Korba', 'pincode' => '495677'],
                ['name' => 'Durg', 'pincode' => '491001'],
                ['name' => 'Rajnandgaon', 'pincode' => '491441'],
                ['name' => 'Raigarh', 'pincode' => '496001'],
                ['name' => 'Jagdalpur', 'pincode' => '494001'],
                ['name' => 'Ambikapur', 'pincode' => '497001'],
                ['name' => 'Dhamtari', 'pincode' => '493773'],
                ['name' => 'Chirmiri', 'pincode' => '497449'],
                ['name' => 'Bhatapara', 'pincode' => '493118'],
                ['name' => 'Mahasamund', 'pincode' => '493445'],
                ['name' => 'Dalli-Rajhara', 'pincode' => '491228'],
                ['name' => 'Kanker', 'pincode' => '494334'],
                ['name' => 'Kawardha', 'pincode' => '491995'],
                ['name' => 'Champa', 'pincode' => '495671'],
                ['name' => 'Naila Janjgir', 'pincode' => '495668'],
                ['name' => 'Dongargarh', 'pincode' => '491445'],
                ['name' => 'Tilda Newra', 'pincode' => '493114'],
                ['name' => 'Mungeli', 'pincode' => '495334'],
                ['name' => 'Manendragarh', 'pincode' => '497442'],
                ['name' => 'Kondagaon', 'pincode' => '494226'],
                ['name' => 'Gobranawapara', 'pincode' => '493881'],
                ['name' => 'Bemetara', 'pincode' => '491335'],
                ['name' => 'Baikunthpur', 'pincode' => '497335'],
                ['name' => 'Balod', 'pincode' => '491226'],
                ['name' => 'Jashpur Nagar', 'pincode' => '496331'],
                ['name' => 'Kharsia', 'pincode' => '496661'],
                ['name' => 'Kirandul', 'pincode' => '494556'],
                ['name' => 'Sakti', 'pincode' => '495689'],
                ['name' => 'Gariaband', 'pincode' => '493889'],
                ['name' => 'Surajpur', 'pincode' => '497229'],
                ['name' => 'Baloda Bazar', 'pincode' => '493332'],
                ['name' => 'Kumhari', 'pincode' => '490042'],
                ['name' => 'Jamul', 'pincode' => '490024'],
                ['name' => 'Dipka', 'pincode' => '495452'],
                ['name' => 'Katghora', 'pincode' => '495445'],
                ['name' => 'Sarangarh', 'pincode' => '496445'],
                ['name' => 'Pendra', 'pincode' => '495117'],
                ['name' => 'Bilha', 'pincode' => '495224'],
                ['name' => 'Arang', 'pincode' => '493441'],
                ['name' => 'Simga', 'pincode' => '493101'],
                ['name' => 'Jhagrakhand', 'pincode' => '497446'],
                ['name' => 'Chhuikhadan', 'pincode' => '491885'],
                ['name' => 'Kunkuri', 'pincode' => '496225'],
                ['name' => 'Pathalgaon', 'pincode' => '496118'],
                ['name' => 'Lormi', 'pincode' => '495115'],
                ['name' => 'Takhatpur', 'pincode' => '495330'],
                ['name' => 'Akaltara', 'pincode' => '495552'],
                ['name' => 'Khairagarh', 'pincode' => '491881'],
                ['name' => 'Bhanpuri', 'pincode' => '493551'],
                ['name' => 'Kharod', 'pincode' => '495556'],
                ['name' => 'Ratanpur', 'pincode' => '495442'],
                ['name' => 'Gandai', 'pincode' => '491888'],
                ['name' => 'Kurud', 'pincode' => '493663'],
                ['name' => 'Ahiwara', 'pincode' => '490036'],
                ['name' => 'Bhaisma', 'pincode' => '495674'],
                ['name' => 'Deori', 'pincode' => '491335'],
                ['name' => 'Dharamjaigarh', 'pincode' => '496116'],
                ['name' => 'Gaurella', 'pincode' => '495117'],
                ['name' => 'Gidam', 'pincode' => '494445'],
                ['name' => 'Kirodimalnagar', 'pincode' => '496005'],
                ['name' => 'Pasan', 'pincode' => '495449'],
                ['name' => 'Pipariya', 'pincode' => '491332'],
                ['name' => 'Sargaon', 'pincode' => '495330'],
                ['name' => 'Shivrinarayan', 'pincode' => '495557'],
                ['name' => 'Sirgiti', 'pincode' => '495004'],
                ['name' => 'Sitapur', 'pincode' => '497111'],
                ['name' => 'Telgaon', 'pincode' => '495557'],
                ['name' => 'Tumgaon', 'pincode' => '493445'],
                ['name' => 'Urla', 'pincode' => '493221'],
                ['name' => 'Vishrampur', 'pincode' => '497226']
            ];

            foreach ($cities as $cityData) {
                City::updateOrCreate(
                    ['name' => $cityData['name'], 'state_id' => $chhattisgarh->id],
                    ['pincode' => $cityData['pincode']]
                );
            }
        }
    }
}
