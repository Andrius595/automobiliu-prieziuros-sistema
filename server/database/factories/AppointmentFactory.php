<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        $car = Car::inRandomOrder()->first() ?? Car::factory()->create();
        $service = Service::inRandomOrder()->first() ?? Service::factory()->create();

        return [
            'car_id' => $car->id,
            'service_id' => $service->id,
            'current_mileage' => $this->faker->numberBetween(1000, 300000),
            'mileage_type' => $this->faker->randomElement([Car::MILEAGE_TYPE_KILOMETERS, Car::MILEAGE_TYPE_MILES]),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed_at' => now(),
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed_at' => now(),
            'completed_at' => now(),
        ]);
    }
    public function registration(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed_at' => null,
            'completed_at' => null,
        ]);
    }
}
