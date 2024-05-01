<?php

namespace Tests\Unit;

use App\Actions\Service\CreateEmployee;
use App\Actions\Service\ListServices;
use App\Actions\Service\UpdateEmployee;
use App\Actions\Service\UpdateService;
use App\Config\PermissionsConfig;
use App\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\TestsHelper;
use Illuminate\Auth\Notifications\ResetPassword;

class ServiceTest extends TestCase
{
    use TestsHelper;

    public function test_if_employee_is_created(): void
    {
        $service = Service::factory()->create();
        $newEmployee = new CreateEmployee();
        $employee = $newEmployee->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test_employee@example.com',
            'service_id' => $service->id,
            'role' => PermissionsConfig::SERVICE_EMPLOYEE_ROLE,
        ]);

        $this->assertNotNull($employee);
        $this->assertTrue($employee->hasRole(PermissionsConfig::SERVICE_EMPLOYEE_ROLE));
        Notification::assertSentTo($employee, ResetPassword::class);
    }

    public function test_if_employee_is_updated(): void
    {
        $service = Service::factory()->create();
        $newEmployee = new CreateEmployee();
        $employee = $newEmployee->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test_employee@example.com',
            'service_id' => $service->id,
            'role' => PermissionsConfig::SERVICE_EMPLOYEE_ROLE,
        ]);

        $updateEmployee = new UpdateEmployee();
        $updateEmployee->update($employee, [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'test_employee@example.com',
            'service_id' => $service->id,
            'role' => PermissionsConfig::SERVICE_ADMIN_ROLE,
        ]);

        $employee->refresh();
        $this->assertEquals('Jane', $employee->first_name);
        $this->assertEquals('Doe', $employee->last_name);
        $this->assertTrue($employee->hasRole(PermissionsConfig::SERVICE_ADMIN_ROLE));
        $this->assertFalse($employee->hasRole(PermissionsConfig::SERVICE_EMPLOYEE_ROLE));
    }

    public function test_if_service_is_updated(): void
    {
        $service = Service::factory()->create();

        $updateService = new UpdateService();
        $fakeFile = UploadedFile::fake()->image('logo.jpg');
        $updateService->update($service, [
            'title' => 'Service1',
            'description' => 'Description',
            'image' => $fakeFile,
        ]);

        $service->refresh();

        $this->assertEquals('Service1', $service->title);
        $this->assertEquals('Description', $service->description);
        $this->assertNotNull($service->logo_path);
    }

    public function test_if_services_are_listed(): void
    {
        $services = Service::factory()->count(3)->create();

        $listServices = new ListServices();

        $services = $listServices->list();

        $this->assertEquals(3, $services->total());
    }
}
