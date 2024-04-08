<?php

namespace Tests\Feature;

use App\Config\PermissionsConfig;
use App\Models\Car;
use App\Models\Reminder;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestsHelper;

class ClientTest extends TestCase
{
    use TestsHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->client = User::factory()->create([
            'service_id' => null,
        ]);
        $this->client->assignRole(PermissionsConfig::CLIENT_ROLE);
    }

    public function test_if_user_can_get_his_cars_list(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);

        $response = $this->get('/api/user/my-cars');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $this->assertEquals($car->id, $response->json('data.0.id'));
        $this->assertEquals(1, $response->json('total'));
    }

    public function test_if_user_can_remove_his_car(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);

        $response = $this->delete('/api/user/my-cars/' . $car->id . '/remove');

        $response->assertStatus(204);
        $this->assertFalse($car->users()->where('id', $this->client->id)->exists());
        $car->refresh();
        $this->assertNull($car->owner_id);
    }

    public function test_if_user_can_share_his_car(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);

        $user = User::factory()->create([
            'service_id' => null,
        ]);

        $response = $this->post('/api/user/my-cars/' . $car->id . '/share', [
            'email' => $user->email,
        ]);

        $response->assertStatus(201);
        $this->assertTrue($car->users()->where('id', $user->id)->exists());
    }

    public function test_if_user_can_transfer_his_car(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);

        $user = User::factory()->create([
            'service_id' => null,
        ]);

        $response = $this->post('/api/user/my-cars/' . $car->id . '/transfer', [
            'email' => $user->email,
        ]);

        $response->assertStatus(204);
        $this->assertEquals($user->id, Car::find($car->id)->owner_id);
    }

    public function test_if_user_can_share_his_car_history(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);

        $response = $this->post('/api/cars/' . $car->id . '/share-history');

        $response->assertStatus(201);
        $this->assertNotNull($car->publicCarHistories()->first());
    }

    public function test_if_user_can_delete_his_car_history(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);
        $car->publicCarHistories()->create([
            'slug' => 'test-slug',
        ]);

        $response = $this->delete('/api/cars/' . $car->id . '/delete-public-history');

        $response->assertStatus(204);
        $this->assertEmpty($car->publicCarHistories);
    }

    public function test_if_user_can_register_new_car(): void
    {
        $this->actingAs($this->client);

        $response = $this->post('/api/user/register-new-car', [
            'make' => 'Volvo',
            'model' => 'V60',
            'vin' => '123234234',
            'plate_no' => 'ABC123',
            'year_of_manufacture' => 2011,
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cars', [
            'make' => 'Volvo',
            'model' => 'V60',
            'vin' => '123234234',
            'plate_no' => 'ABC123',
            'year_of_manufacture' => 2011,
            'mileage_type' => Car::MILEAGE_TYPE_KILOMETERS,
        ]);
    }

    public function test_if_user_can_get_his_car_history(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);
        $this->createCompletedAppointmentForCarId($car->id);

        $response = $this->get('/api/cars/' . $car->id . '/history');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_if_user_can_get_his_car_public_urls(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);
        $car->publicCarHistories()->create([
            'slug' => 'test-slug',
        ]);

        $response = $this->get('/api/cars/' . $car->id . '/public-urls');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_if_user_can_get_his_car_reminders(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);
        Reminder::factory()->create([
            'car_id' => $car->id,
        ]);


        $response = $this->get('/api/cars/' . $car->id . '/reminders');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_if_user_can_create_car_reminder(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);

        $response = $this->post('/api/cars/' . $car->id . '/reminders', [
            'title' => 'Test reminder',
            'description' => 'Test description',
            'interval' => 1,
            'type' => Reminder::TYPE_MONTHS,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reminders', [
            'car_id' => $car->id,
            'title' => 'Test reminder',
            'description' => 'Test description',
            'interval' => 1,
            'type' => Reminder::TYPE_MONTHS,
        ]);
    }

    public function test_if_user_can_get_reminder(): void
    {
        $this->actingAs($this->client);

        $reminder = Reminder::factory()->create();

        $response = $this->get('/api/cars/reminders/' . $reminder->id);

        $response->assertStatus(200);
        $this->assertEquals($reminder->id, $response->json('id'));
    }

    public function test_if_user_can_update_reminder(): void
    {
        $this->actingAs($this->client);

        $reminder = Reminder::factory()->create();

        $response = $this->put('/api/cars/reminders/' . $reminder->id, [
            'car_id' => $reminder->car_id,
            'title' => 'Updated title',
            'description' => 'Updated description',
            'interval' => 2,
            'type' => Reminder::TYPE_YEARS,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'title' => 'Updated title',
            'description' => 'Updated description',
            'interval' => 2,
            'type' => Reminder::TYPE_YEARS,
        ]);
    }

    public function test_if_user_can_delete_reminder(): void
    {
        $this->actingAs($this->client);

        $reminder = Reminder::factory()->create();

        $response = $this->delete('/api/cars/reminders/' . $reminder->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('reminders', [
            'id' => $reminder->id,
        ]);
    }

    public function test_if_user_can_register_for_appointment(): void
    {
        $this->actingAs($this->client);

        $car = $this->createCarForUserId($this->client->id);
        $service = Service::factory()->create();

        $response = $this->post('/api/services/' . $service->id . '/register', [
            'car_id' => $car->id,
            'mileage_type' => $car->mileage_type,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('appointments', [
            'car_id' => $car->id,
            'service_id' => $service->id,
            'mileage_type' => $car->mileage_type,
            'confirmed_at' => null,
            'completed_at' => null,
        ]);
    }

}
