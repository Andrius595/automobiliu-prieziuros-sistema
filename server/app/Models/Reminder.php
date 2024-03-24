<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const TYPE_DAYS = 0;
    const TYPE_WEEKS = 1;
    const TYPE_MONTHS = 2;
    const TYPE_YEARS = 3;
    const TYPE_MILEAGE = 4; // ???
}
