<?php

namespace App\Actions\Service;

use App\Actions\ListsRecords;
use App\Models\Service;

class ListServices extends ListsRecords
{
    public string $model = Service::class;
}
