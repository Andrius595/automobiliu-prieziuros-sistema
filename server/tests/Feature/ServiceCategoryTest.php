<?php

namespace Tests\Feature;

use App\Config\PermissionsConfig;
use App\Models\ServiceCategory;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;
use Tests\TestsHelper;

class ServiceCategoryTest extends TestCase
{
    use TestsHelper;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create([
            'service_id' => null,
        ]);
        $this->admin->assignRole(PermissionsConfig::SYSTEM_ADMIN_ROLE);
    }

    public function test_if_admin_can_create_service_category(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/api/service-categories', [
            'name' => 'Service Category 1',
        ]);

        $response->assertStatus(201);
    }

    public function test_if_admin_can_update_service_category(): void
    {
        $this->actingAs($this->admin);

        $serviceCategory = ServiceCategory::factory()->create();

        $response = $this->put("/api/service-categories/$serviceCategory->id", [
            'name' => 'Service Category 2',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('service_categories', [
            'id' => $serviceCategory->id,
            'name' => 'Service Category 2',
        ]);
    }

    public function test_if_admin_can_delete_service_category(): void
    {
        $this->actingAs($this->admin);

        $serviceCategory = ServiceCategory::factory()->create();

        $response = $this->delete("/api/service-categories/$serviceCategory->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('service_categories', [
            'id' => $serviceCategory->id,
        ]);
    }

    public function test_if_admin_can_get_service_categories_list(): void
    {
        $this->actingAs($this->admin);

        ServiceCategory::factory()->count(5)->create();

        $response = $this->get('/api/service-categories');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_if_admin_can_get_service_category(): void
    {
        $this->actingAs($this->admin);

        $serviceCategory = ServiceCategory::factory()->create();

        $response = $this->get("/api/service-categories/$serviceCategory->id");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $serviceCategory->id,
            'name' => $serviceCategory->name,
        ]);
    }

    public function test_if_admin_can_get_service_categories_list_for_select(): void
    {
        $this->actingAs($this->admin);

        ServiceCategory::factory()->count(5)->create();

        $response = $this->get('/api/service-categories/list-for-select');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }
}
