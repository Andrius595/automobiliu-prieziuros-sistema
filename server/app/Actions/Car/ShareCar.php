<?php

namespace App\Actions\Car;

use App\Models\Car;
use App\Models\User;

class ShareCar
{
    public function share(Car $car, $email): void
    {
        $user = User::where('email', $email)->firstOrFail();

        $car->users()->attach($user->id);
    }
}
