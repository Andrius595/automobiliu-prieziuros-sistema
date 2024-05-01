<?php

namespace Tests\Unit;

use App\Actions\Appointment\ListAppointments;
use App\Actions\Car\CreateNewCar;
use App\Actions\Car\DeletePublicCarHistory;
use App\Actions\Car\ListCars;
use App\Actions\Car\ShareCar;
use App\Actions\Car\ShareCarHistory;
use App\Actions\Car\TransferCar;
use App\Actions\Car\UpdateCar;
use App\Models\PublicCarHistory;
use App\Models\Service;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Tests\TestsHelper;

class CarTest extends TestCase
{
    use TestsHelper;

    public function test_if_car_is_created(): void
    {
        $user = User::factory()->create();
        $newCar = new CreateNewCar();
        $car = $newCar->create([
            'vin' => '12345678901234567',
            'plate_no' => 'ABC123',
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year_of_manufacture' => 2010,
            'color' => 'Red',
            'mileage_type' => 1,
            'owner_id' => $user->id,
        ]);

        $this->assertNotNull($car);
        $this->assertEquals('12345678901234567', $car->vin);
        $this->assertEquals('ABC123', $car->plate_no);
        $this->assertEquals('Toyota', $car->make);
        $this->assertEquals('Corolla', $car->model);
        $this->assertEquals(2010, $car->year_of_manufacture);
        $this->assertEquals('Red', $car->color);
        $this->assertEquals(1, $car->mileage_type);
        $this->assertEquals($user->id, $car->owner_id);
    }

    // duplicated vin
    public function test_if_car_is_not_created_due_to_duplicated_vin(): void
    {
        $user = User::factory()->create();
        $newCar = new CreateNewCar();
        $car1 = $newCar->create([
            'vin' => '12345678901234567',
            'plate_no' => 'ABC123',
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year_of_manufacture' => 2010,
            'color' => 'Red',
            'mileage_type' => 1,
            'owner_id' => $user->id,
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(trans('validation.unique', ['attribute' => 'vin']));
        $car2 = $newCar->create([
            'vin' => '12345678901234567',
            'plate_no' => 'ABC123',
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year_of_manufacture' => 2010,
            'color' => 'Red',
            'mileage_type' => 1,
            'owner_id' => $user->id,
        ]);

        $this->assertNotNull($car1);
        $this->assertNull($car2);
    }

    public function test_if_public_car_history_is_created(): void
    {
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);

        $newHistory = new ShareCarHistory();
        $history = $newHistory->share($car);

        $this->assertNotNull($history);
        $this->assertEquals($car->id, $history->car_id);
        $this->assertNotNull($history->slug);
    }

    public function test_if_public_car_history_is_deleted(): void
    {
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);

        $newHistory = new ShareCarHistory();
        $newHistory->share($car);

        $this->assertCount(1, PublicCarHistory::all());

        $deleteHistory = new DeletePublicCarHistory();
        $deleteHistory->delete($car);

        $this->assertCount(0, PublicCarHistory::all());
    }

    public function test_if_cars_are_listed(): void
    {
        $user = User::factory()->create();
        $this->createCarForUserId($user->id);
        $this->createCarForUserId($user->id);

        $list_cars = new ListCars();

        $cars = $list_cars->list();

        $this->assertEquals(2, $cars->total());
    }

    public function test_if_car_is_updated()
    {
        $user = User::factory()->create();
        $car = $this->createCarForUserId($user->id);

        $updateCar = new UpdateCar();

        $data = [
            'vin' => '12345678901234567',
            'plate_no' => 'ABC123',
            'make' => 'Toyota',
            'model' => 'Corolla',
            'year_of_manufacture' => 2010,
            'color' => 'Red',
            'mileage_type' => 1,
        ];

        $updateCar->update($car, $data);

        $car->refresh();

        $this->assertEquals('12345678901234567', $car->vin);
        $this->assertEquals('ABC123', $car->plate_no);
        $this->assertEquals('Toyota', $car->make);
        $this->assertEquals('Corolla', $car->model);
        $this->assertEquals(2010, $car->year_of_manufacture);
        $this->assertEquals('Red', $car->color);
        $this->assertEquals(1, $car->mileage_type);
        $this->assertEquals($user->id, $car->owner_id);
    }

    public function test_if_car_is_transferred(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $car = $this->createCarForUserId($user->id);

        $transferCar = new TransferCar(new UpdateCar());

        $transferCar->transfer($car, $user2->email);

        $car->refresh();

        $this->assertEquals($user2->id, $car->owner_id);
    }
}
