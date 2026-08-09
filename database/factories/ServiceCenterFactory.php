<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceCenter>
 */
class ServiceCenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $municipality = \App\Models\Municipality::inRandomOrder()->first();
        $serviceNetwork = \App\Models\ServiceNetwork::inRandomOrder()->first();
        $city = \App\Models\City::find($municipality->city_id);
        $city_c = json_decode($city->coord);
        $latMin = (int)$city_c->lat - (125 / 1000);
        $latMax = (int)$city_c->lat + (125 / 1000);
        $longMin = (int)$city_c->long - (125 / 1000);
        $longMax = (int)$city_c->long + (125 / 1000);
        $photos = [];
        for ($i = 0; $i < rand(10, 30); $i++) {
            $photos[] = ['name' => 'https://picsum.photos/', 'sizes' => []];
        }

        $additionalPhones = [];
        if (rand(0, 3) == 3) {
            for ($i = 0; $i < rand(0, 7); $i++) {
                $additionalPhones[] = fake()->phoneNumber();
            }
        }

        $web = [];
        for ($i = 0; $i < rand(1, 5); $i++) {
            $web[] = fake()->safeEmailDomain();
        }

        $moreSocials = [];
        if (rand(0, 3) == 3) {
            for ($i = 0; $i < rand(0, 7); $i++) {
                $moreSocials['name_' . ($i + 1)] = '#';
            }
        }

        $emails = [];
        for ($i = 0; $i < rand(1, 3); $i++) {
            $emails[] = fake()->email();
        }

        $ratingArray = array(
            rand(11, 50) / 10,
            rand(11, 50) / 10,
            rand(11, 50) / 10,
            rand(11, 50) / 10,
        );

        $name = 'service_center';
        for ($i = 0; $i < 2; $i++) {
            $name .= '_' . fake()->word();
        }

        return [
            'region_id' => $municipality->region_id,
            'city_id' => $municipality->city_id,
            'area_id' => $municipality->area_id,
            'municipality_id' => $municipality->id,
            'service_network_id' =>rand(0,3) > 1 ? $serviceNetwork->id : null,
            'logo' =>  'https://picsum.photos/',
            'title' => 'service_center_title_' . fake()->word(),
            'name' => $name,
            'address' => fake()->streetAddress(),
            'description' => implode('', fake()->paragraphs()),
            'zip' => fake()->postcode(),
            'coord' => json_encode(array(
                'lat' => fake()->latitude($latMin, $latMax),
                'long' => fake()->longitude($longMin, $longMax)
            )),
            'photos' => json_encode($photos),
            'phone' => fake()->e164PhoneNumber(),
            'additional_phones' => json_encode($additionalPhones),
            'whatsapp' => fake()->phoneNumber(),
            'telegram' => fake()->phoneNumber(),
            'vk' => fake()->phoneNumber(),
            'web' => json_encode($web),
            'more_socials' => json_encode($moreSocials),
            'emails' => json_encode($emails),
            'open_24_hours' => rand(0, 100) < 15,
            'online_estimate' => rand(0, 100) < 50,
            'warranty' => rand(0, 100) < 80,
            'onsite_repair' => rand(0, 100) < 27,
            'courier' => rand(0, 100) < 45,
            'original_parts' => rand(0, 100) < 55,
            'buyback' => rand(0, 100) < 40,
            'trade_in' => rand(0, 100) < 30,
            'buy_for_parts' => rand(0, 100) < 35,
            'average_rating' => number_format(array_sum($ratingArray) / count($ratingArray), 1, '.'),
        ];
    }
}
