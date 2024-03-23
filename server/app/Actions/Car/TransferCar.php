<?php

namespace App\Actions\Car;

use App\Models\Car;
use App\Models\PublicCarHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferCar
{
    public function transfer(Car $car, string $email): bool
    {
        $updateCar = new UpdateCar();
        $otherUser = User::where('email', $email)->firstOrFail();
        $data = [
            'owner_id' => $otherUser->id,
            'owner_confirmed_at' => null,
        ];

        DB::table('cars_users')
            ->where('car_id', $car->id)
            ->delete();
        PublicCarHistory::where('car_id', $car->id)->delete();

        return $updateCar->update($car, $data);
    }

}
