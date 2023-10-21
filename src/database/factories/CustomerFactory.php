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
        $first_name = mb_convert_kana(fake()->firstKanaName, 'c');
        $last_name = mb_convert_kana(fake()->lastKanaName, 'c');
        $full_name = $last_name . $first_name;

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'full_name' => $full_name,
            'town_id' => \App\Models\Town::inRandomOrder()->first()->id,
            'address_number' => implode('-', str_split(fake()->randomNumber(3, true))),
            'room_number' => fake()->buildingNumber(),
            'is_dropoff_possible' => fake()->boolean,
            'description' => fake()->realText,
        ];
    }
}
