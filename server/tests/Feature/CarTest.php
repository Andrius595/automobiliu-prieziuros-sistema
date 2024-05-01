<?php

namespace Tests\Feature;

use App\Config\PermissionsConfig;
use App\Models\Car;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\TestsHelper;

class CarTest extends TestCase
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

    public function test_if_admin_can_list_cars(): void
    {
        $this->actingAs($this->admin);

        $cars = Car::factory()->count(5)->create();

        $response = $this->get('/api/cars');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
        $this->assertEquals($cars[0]->id, $response->json('data.0.id'));
        $this->assertEquals(5, $response->json('total'));
    }

    public function test_if_admin_can_view_car(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create();

        $response = $this->get("/api/cars/$car->id");

        $response->assertStatus(200);
        $this->assertEquals($car->id, $response->json('id'));
    }

    public function test_if_admin_can_update_car(): void
    {
        $this->actingAs($this->admin);

        $car = Car::factory()->create();

        $response = $this->put("/api/cars/$car->id", [
            'make' => 'Brand 2',
            'model' => 'Model 2',
            'year_of_manufacture' => 2022,
            'plate_no' => 'ABC123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'make' => 'Brand 2',
            'model' => 'Model 2',
            'year_of_manufacture' => 2022,
            'plate_no' => 'ABC123',
        ]);
    }
}
