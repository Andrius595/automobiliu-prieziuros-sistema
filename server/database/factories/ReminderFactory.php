<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Reminder>
 */
class ReminderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $car = Car::inRandomOrder()->first() ?? Car::factory()->create();
        return [
            'car_id' => $car->id,
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'interval' => $this->faker->numberBetween(1, 100),
            'type' => $this->faker->randomElement([
                Reminder::TYPE_MILEAGE,
                Reminder::TYPE_DAYS,
                Reminder::TYPE_WEEKS,
                Reminder::TYPE_MONTHS,
                Reminder::TYPE_YEARS,
            ]),
            'last_reminded_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
