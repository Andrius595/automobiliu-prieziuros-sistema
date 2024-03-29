<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = City::inRandomOrder()->first() ?? City::factory()->create();
        return [
            'title' => $this->faker->company(),
            'address' => $this->faker->address,
            'description' => $this->faker->text,
            'city_id' => $city->id,
        ];
    }
}
