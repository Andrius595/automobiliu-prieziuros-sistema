<?php

namespace App\Actions\Car;

use App\Models\Car;
use App\Models\User;

class RemoveCarFromUser
{
    public function __construct(private readonly UpdateCar $updateCar)
    {
    }

    public function remove(Car $car, int $user_id): void
    {
        if ($car->owner_id === $user_id) {
            $data = [
                'owner_id' => null,
            ];
            $this->updateCar->update($car, $data);
            $car->users()->detach();

            return;
        }

        $car->users()->detach($user_id);
    }
}
