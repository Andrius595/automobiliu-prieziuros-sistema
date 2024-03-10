<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    public const MILEAGE_TYPE_KILOMETERS = 0;
    public const MILEAGE_TYPE_MILES = 1;
}
