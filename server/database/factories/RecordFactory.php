<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Record>
 */
class RecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $appointment = Appointment::inRandomOrder()->first() ?? Appointment::factory()->create();
        return [
            'appointment_id' => $appointment->id,
            'short_description' => $this->faker->sentence,
        ];
    }
}
