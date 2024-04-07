<?php

namespace App\Actions\Car;

use App\Models\Car;
use App\Models\PublicCarHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferCar
{
    public function __construct(private readonly UpdateCar $updateCar)
    {
    }

    public function transfer(Car $car, string $email): bool
    {
        $otherUser = User::where('email', $email)->firstOrFail();
        $data = [
            'owner_id' => $otherUser->id,
            'owner_confirmed_at' => null,
        ];

        $car->users()->detach();

        PublicCarHistory::where('car_id', $car->id)->delete();

        return $this->updateCar->update($car, $data);
    }

}
