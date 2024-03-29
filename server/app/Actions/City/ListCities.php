<?php

namespace App\Actions\City;

use App\Actions\ListsRecords;
use App\Models\City;
use App\Models\Reminder;

class ListCities extends ListsRecords
{
    public string $model = City::class;
}
