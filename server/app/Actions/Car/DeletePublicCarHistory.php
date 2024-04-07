<?php

namespace App\Actions\Car;

use App\Models\Car;
use App\Models\PublicCarHistory;

class DeletePublicCarHistory
{
    public function delete(Car $car): bool
    {
        return PublicCarHistory::where('car_id', $car->id)->delete();
    }
}
