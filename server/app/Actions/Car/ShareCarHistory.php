<?php

namespace App\Actions\Car;

use App\Models\Car;
use App\Models\PublicCarHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShareCarHistory
{
    public function share(Car $car): PublicCarHistory
    {
        $slug = Str::random(20);

        return PublicCarHistory::create([
            'car_id' => $car->id,
            'slug' => $slug,
        ]);
    }
}
