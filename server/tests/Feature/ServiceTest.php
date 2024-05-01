<?php

namespace Tests\Feature;

use App\Config\PermissionsConfig;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\City;
use App\Models\Record;
use App\Models\User;
use Aws\Lambda\LambdaClient;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;
use Tests\TestsHelper;

class ServiceTest extends TestCase
{
    use TestsHelper;

    private User $admin;
    private User $serviceAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create([
            'service_id' => null,
        ]);
        $this->admin->assignRole(PermissionsConfig::SYSTEM_ADMIN_ROLE);

        $this->serviceAdmin = User::factory()->create();
        $this->serviceAdmin->assignRole(PermissionsConfig::SERVICE_ADMIN_ROLE);
    }

    public function test_if_admin_can_create_service(): void
    {
        $this->actingAs($this->admin);

        $city = City::factory()->create();

        $response = $this->post('/api/services', [
            'title' => 'Service 1',
            'city_id' => $city->id,
            'address' => 'Address 1',
            'first_name' => 'Service',
            'last_name' => 'Administrator',
            'email' => 'service_admin@example.com',
        ]);

        $response->assertStatus(201);
    }

    public function test_if_service_admin_can_get_registrations_list(): void
    {
        $this->actingAs($this->serviceAdmin);

        Appointment::factory()->count(5)->registration()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);
        Appointment::factory()->count(3)->completed()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);

        $response = $this->get('/api/service/registrations');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_if_service_admin_can_confirm_registration(): void
    {
        $this->actingAs($this->serviceAdmin);

        $appointment = Appointment::factory()->registration()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);

        $response = $this->post('/api/service/registrations/' . $appointment->id . '/confirm');

        $response->assertStatus(200);
        $appointment->refresh();
        $this->assertNotNull($appointment->confirmed_at);
    }

    public function test_if_service_admin_can_create_appointment(): void
    {
        $this->actingAs($this->serviceAdmin);

        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);

        $response = $this->post('/api/service/appointments/create-appointment', [
            'car_id' => $car->id,
            'current_mileage' => 10000,
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('appointments', [
            'car_id' => $car->id,
            'service_id' => $this->serviceAdmin->service_id,
            'current_mileage' => 10000,
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
        ]);
    }

    public function test_if_service_admin_can_get_active_appointments(): void
    {
        $this->actingAs($this->serviceAdmin);

        Appointment::factory()->count(5)->confirmed()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);
        Appointment::factory()->count(3)->completed()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);

        $response = $this->get('/api/service/appointments/active');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_if_service_admin_can_get_completed_appointments(): void
    {
        $this->actingAs($this->serviceAdmin);

        Appointment::factory()->count(5)->completed()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);
        Appointment::factory()->count(3)->confirmed()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);

        $response = $this->get('/api/service/appointments/completed');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_if_service_admin_can_get_employees_list(): void
    {
        $this->actingAs($this->serviceAdmin);

        $employees = User::factory()->count(5)->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);

        $response = $this->get('/api/services/' . $this->serviceAdmin->service_id . '/employees');

        $response->assertStatus(200);
        $response->assertJsonCount(6, 'data');
    }

    public function test_if_service_admin_can_create_employee(): void
    {
        $this->actingAs($this->serviceAdmin);

        $response = $this->post('/api/services/' . $this->serviceAdmin->service_id . '/employees', [
            'first_name' => 'Employee',
            'last_name' => 'One',
            'email' => 'employee@example.test',
            'role' => PermissionsConfig::SERVICE_EMPLOYEE_ROLE,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'first_name' => 'Employee',
            'last_name' => 'One',
            'service_id' => $this->serviceAdmin->service_id,
            'email' => 'employee@example.test',
        ]);
    }

    public function test_if_service_admin_can_get_employee(): void
    {
        $this->actingAs($this->serviceAdmin);

        $employee = User::factory()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);
        $employee->assignRole(PermissionsConfig::SERVICE_EMPLOYEE_ROLE);

        $response = $this->get('/api/services/' . $this->serviceAdmin->service_id . '/employees/' . $employee->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'role' => PermissionsConfig::SERVICE_EMPLOYEE_ROLE,
        ]);
    }

    public function test_if_service_admin_can_update_employee(): void
    {
        $this->actingAs($this->serviceAdmin);

        $employee = User::factory()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);
        $employee->assignRole(PermissionsConfig::SERVICE_EMPLOYEE_ROLE);

        $response = $this->put('/api/services/' . $this->serviceAdmin->service_id . '/employees/' . $employee->id, [
            'first_name' => 'Employee',
            'last_name' => 'One',
            'email' => 'updated@example.test',
            'role' => PermissionsConfig::SERVICE_EMPLOYEE_ROLE,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'email' => 'updated@example.test',
        ]);
    }

    public function test_if_service_admin_can_delete_employee(): void
    {
        $this->actingAs($this->serviceAdmin);

        $employee = User::factory()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);
        $employee->assignRole(PermissionsConfig::SERVICE_EMPLOYEE_ROLE);

        $response = $this->delete('/api/services/' . $this->serviceAdmin->service_id . '/employees/' . $employee->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $employee->id,
        ]);
    }

    public function test_if_service_admin_can_get_service(): void
    {
        $this->actingAs($this->serviceAdmin);

        $response = $this->get('/api/services/' . $this->serviceAdmin->service_id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->serviceAdmin->service_id,
            'title' => $this->serviceAdmin->service->title,
            'city_id' => $this->serviceAdmin->service->city_id,
            'address' => $this->serviceAdmin->service->address,
        ]);
    }

    public function test_if_service_admin_can_update_service(): void
    {
        $this->actingAs($this->serviceAdmin);

        $response = $this->put('/api/services/' . $this->serviceAdmin->service_id, [
            'title' => 'Service 2',
            'city_id' => $this->serviceAdmin->service->city_id,
            'address' => 'Address 2',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('services', [
            'id' => $this->serviceAdmin->service_id,
            'title' => 'Service 2',
            'address' => 'Address 2',
        ]);
    }

    public function test_if_service_admin_can_get_service_appointment(): void
    {
        $this->actingAs($this->serviceAdmin);

        $appointment = Appointment::factory()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);

        $response = $this->get('/api/service/appointments/' . $appointment->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $appointment->id,
            'car_id' => $appointment->car_id,
            'service_id' => $appointment->service_id,
        ]);
    }

//    public function test_if_service_admin_can_complete_appointment(): void
//    {
//        $this->actingAs($this->serviceAdmin);
//
//        $this->mock(LambdaClient::class, function ($mock) {
//            $mock->shouldReceive('__construct')->andReturn(null);
//            $mock->shouldReceive('invoke')->andReturn(null);
//        });
//
//        $appointment = Appointment::factory()->create([
//            'service_id' => $this->serviceAdmin->service_id,
//        ]);
//
//        $response = $this->post('/api/service/appointments/' . $appointment->id . '/complete');
//
//        $response->assertStatus(200);
//        $appointment->refresh();
//        $this->assertNotNull($appointment->completed_at);
//    }

    public function test_if_service_admin_can_get_car_by_vin(): void
    {
        $this->actingAs($this->serviceAdmin);

        $car = Car::factory()->create();

        $response = $this->get('/api/cars/vin/' . $car->vin);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $car->id,
            'vin' => $car->vin,
        ]);
    }

    public function test_if_service_admin_can_create_record(): void
    {
        $this->actingAs($this->serviceAdmin);

        $car = Car::factory()->create();
        $appointment = Appointment::factory()->confirmed()->create([
            'car_id' => $car->id,
            'service_id' => $this->serviceAdmin->service_id,
        ]);

        $response = $this->post('/api/service/appointments/' . $appointment->id . '/records', [
            'short_description' => 'Description',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('records', [
            'id' => $response->json('id'),
            'appointment_id' => $appointment->id,
            'short_description' => 'Description',
        ]);
    }

    public function test_if_service_admin_can_delete_record(): void
    {
        $this->actingAs($this->serviceAdmin);

        $record = Record::factory()->create();

        $response = $this->delete('/api/service/appointments/records/' . $record->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('records', [
            'id' => $record->id,
        ]);
    }

    public function test_if_service_admin_can_get_record(): void
    {
        $this->actingAs($this->serviceAdmin);

        $record = Record::factory()->create();

        $response = $this->get('/api/service/appointments/records/' . $record->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $record->id,
            'appointment_id' => $record->appointment_id,
            'short_description' => $record->short_description,
        ]);
    }

    public function test_if_service_admin_can_update_record(): void
    {
        $this->actingAs($this->serviceAdmin);

        $record = Record::factory()->create();

        $response = $this->put('/api/service/appointments/records/' . $record->id, [
            'short_description' => 'Description',
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseHas('records', [
            'id' => $record->id,
            'short_description' => 'Description',
        ]);
    }

    //getAppointmentRecords
    public function test_if_service_admin_can_get_appointment_records(): void
    {
        $this->actingAs($this->serviceAdmin);

        $appointment = Appointment::factory()->create([
            'service_id' => $this->serviceAdmin->service_id,
        ]);
        Record::factory()->count(5)->create([
            'appointment_id' => $appointment->id,
        ]);

        $response = $this->get('/api/service/appointments/' . $appointment->id . '/records');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }
}
