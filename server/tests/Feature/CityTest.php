<?php

namespace Tests\Feature;

use App\Config\PermissionsConfig;
use App\Models\City;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;
use Tests\TestsHelper;

class CityTest extends TestCase
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

    public function test_if_admin_can_create_city(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/api/cities', [
            'name' => 'City 1',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cities', [
            'name' => 'City 1',
        ]);
    }

    public function test_if_admin_can_update_city(): void
    {
        $this->actingAs($this->admin);

        $city = City::factory()->create();

        $response = $this->put("/api/cities/$city->id", [
            'name' => 'City 2',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'name' => 'City 2',
        ]);
    }

    public function test_if_admin_can_delete_city(): void
    {
        $this->actingAs($this->admin);

        $city = City::factory()->create();

        $response = $this->delete("/api/cities/$city->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('cities', [
            'id' => $city->id,
        ]);
    }

    public function test_if_admin_can_get_cities_list(): void
    {
        $this->actingAs($this->admin);

        City::factory()->count(5)->create();

        $response = $this->get('/api/cities');

        $response->assertStatus(200);
        // 1 is created while seeding roles
        $response->assertJsonCount(6, 'data');
    }

    public function test_if_admin_can_get_city(): void
    {
        $this->actingAs($this->admin);

        $city = City::factory()->create();

        $response = $this->get("/api/cities/$city->id");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $city->id,
            'name' => $city->name,
        ]);
    }

    public function test_if_admin_can_get_cities_list_for_select(): void
    {
        $this->actingAs($this->admin);

        City::factory()->count(5)->create();

        $response = $this->get('/api/cities/list-for-select');

        $response->assertStatus(200);
        // 1 is created while seeding roles
        $response->assertJsonCount(6);
    }
}
