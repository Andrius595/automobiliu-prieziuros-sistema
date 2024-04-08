<?php

namespace Tests\Unit;

use App\Actions\Car\CreateNewCar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Tests\TestsHelper;

class CarTest extends TestCase
{
    use TestsHelper;

    public function test_if_car_is_created(): void
    {
        $user = \App\Models\User::factory()->create();
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
        $user = \App\Models\User::factory()->create();
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
        $this->expectExceptionMessage('The vin has already been taken.');
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
}
