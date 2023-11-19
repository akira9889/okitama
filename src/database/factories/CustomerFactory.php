<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $last_name = fake()->lastName;
        $first_name = fake()->firstName;
        $full_name = $last_name . $first_name;

        $first_kana = mb_convert_kana(fake()->firstKanaName, 'c');
        $last_kana = mb_convert_kana(fake()->lastKanaName, 'c');
        $full_kana = $last_kana . $first_kana;

        $buildingName = fake()->optional(0.5)->secondaryAddress();
        if ($buildingName) {
            $buildingName = preg_replace('/\d+号/', '', $buildingName);
        }

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'full_name' => $full_name,
            'first_kana' => $first_kana,
            'last_kana' => $last_kana,
            'full_kana' => $full_kana,
            'town_id' => \App\Models\Town::inRandomOrder()->first()->id,
            'address_number' => implode('-', str_split(fake()->randomNumber(3, true))),
            'building_name' => $buildingName,
            'room_number' => fake()->optional(0.5)->buildingNumber(),
            'description' => fake()->optional(0.5)->realText,
        ];
    }
}
