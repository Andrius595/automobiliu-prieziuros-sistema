<?php

namespace Tests;

use App\Models\Appointment;
use App\Models\Car;
use Aws\Lambda\LambdaClient;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

trait TestsHelper
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Mail::fake();
        Event::fake();
        Bus::fake();
        Notification::fake();
        Storage::fake();
        Queue::fake();
    }

    private function createCarForUserId(int $userId): Car
    {
        $car = Car::factory()->create([
            'owner_id' => $userId,
        ]);
        $car->users()->attach($userId);

        return $car;
    }

    private function createCompletedAppointmentForCarId(int $carId): Appointment
    {
        return Appointment::factory()->completed()->completed()->create([
            'car_id' => $carId,
        ]);
    }

    private function createConfirmedAppointmentForCarId(int $carId): Appointment
    {
        return Appointment::factory()->confirmed()->create([
            'car_id' => $carId,
        ]);
    }

    private function createRegistrationAppointmentForCarId(int $carId): Appointment
    {
        return Appointment::factory()->registration()->create([
            'car_id' => $carId,
        ]);
    }
}
