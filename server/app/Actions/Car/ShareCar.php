<?php

namespace App\Actions\Car;

use App\Actions\UpdatesRecord;
use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ShareCar
{
    public function share(Car $car, $email): bool
    {
        $user = User::where('email', $email)->firstOrFail();

        return DB::table('cars_users')->insert([
            'car_id' => $car->id,
            'user_id' => $user->id,
        ]);
    }
}
