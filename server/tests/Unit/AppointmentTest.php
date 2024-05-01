<?php

namespace Tests\Unit;

use App\Actions\Appointment\CompleteAppointment;
use App\Actions\Appointment\CreateNewAppointment;
use App\Actions\Appointment\ListAppointments;
use App\Actions\Appointment\UpdateAppointment;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\Service;
use App\Models\User;
use Aws\Lambda\LambdaClient;
use Aws\Result;
use Tests\TestCase;
use Tests\TestsHelper;

class AppointmentTest extends TestCase
{
    use TestsHelper;

    public function test_if_appointment_is_completed(): void
    {
        $this->mock(LambdaClient::class, function ($mock) {
            $mock->shouldReceive('invoke')->andReturn(null);
        });

        $completeAppointment = new CompleteAppointment(new UpdateAppointment());
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);
        $appointment = $this->createConfirmedAppointmentForCarId($car->id);

        $this->assertNull($appointment->completed_at);

        $completeAppointment->complete($appointment);
        $appointment->refresh();

        $this->assertNotNull($appointment->completed_at);
    }

    public function test_if_appointment_is_created(): void
    {
        $newAppointment = new CreateNewAppointment();
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);
        $service = Service::factory()->create();

        $appointment = $newAppointment->create([
            'car_id' => $car->id,
            'service_id' => $service->id,
            'current_mileage' => 20000,
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
        ]);

        $this->assertNotNull($appointment);
        $this->assertEquals($car->id, $appointment->car_id);
        $this->assertEquals($service->id, $appointment->service_id);
        $this->assertEquals(20000, $appointment->current_mileage);
        $this->assertEquals(Car::MILEAGE_TYPE_KILOMETERS, $appointment->mileage_type);
    }

    public function test_if_all_appointments_are_listed(): void
    {
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);
        $this->createCompletedAppointmentForCarId($car->id);
        $this->createConfirmedAppointmentForCarId($car->id);
        $this->createRegistrationAppointmentForCarId($car->id);


        $listAppointments = new ListAppointments();
        $response = $listAppointments->list();

        $this->assertEquals(3, $response->total());
    }

    public function test_if_registration_appointments_are_listed(): void
    {
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);
        $this->createCompletedAppointmentForCarId($car->id);
        $this->createConfirmedAppointmentForCarId($car->id);
        $registration = $this->createRegistrationAppointmentForCarId($car->id);


        $listAppointments = new ListAppointments();
        $response = $listAppointments->list([
            'registrations' => true,
        ]);

        $this->assertEquals(1, $response->total());
        $this->assertEquals($registration->id, $response->items()[0]->id);
    }

    public function test_if_active_appointments_are_listed(): void
    {
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);
        $this->createCompletedAppointmentForCarId($car->id);
        $active = $this->createConfirmedAppointmentForCarId($car->id);
        $this->createRegistrationAppointmentForCarId($car->id);


        $listAppointments = new ListAppointments();
        $response = $listAppointments->list([
            'active' => true,
        ]);

        $this->assertEquals(1, $response->total());
        $this->assertEquals($active->id, $response->items()[0]->id);
    }

    public function test_if_completed_appointments_are_listed(): void
    {
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);
        $completed = $this->createCompletedAppointmentForCarId($car->id);
        $this->createConfirmedAppointmentForCarId($car->id);
        $this->createRegistrationAppointmentForCarId($car->id);


        $listAppointments = new ListAppointments();
        $response = $listAppointments->list([
            'completed' => true,
        ]);

        $this->assertEquals(1, $response->total());
        $this->assertEquals($completed->id, $response->items()[0]->id);
    }

    public function test_if_appointment_is_updated(): void
    {
        $updateAppointment = new UpdateAppointment();
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create([
            'car_id' => $car->id,
            'service_id' => $service->id,
            'current_mileage' => 10000,
            'mileage_type' => Car::MILEAGE_TYPE_MILES,
        ]);

        $updateAppointment->update($appointment, [
            'current_mileage' => 20000,
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
        ]);

        $appointment->refresh();

        $this->assertEquals($car->id, $appointment->car_id);
        $this->assertEquals($service->id, $appointment->service_id);
        $this->assertEquals(20000, $appointment->current_mileage);
        $this->assertEquals(Car::MILEAGE_TYPE_KILOMETERS, $appointment->mileage_type);
    }
}
