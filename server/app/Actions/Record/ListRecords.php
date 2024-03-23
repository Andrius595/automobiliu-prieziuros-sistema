<?php

namespace App\Actions\Record;

use App\Actions\ListsRecords;
use App\Models\Record;
use Illuminate\Pagination\LengthAwarePaginator;

class ListRecords extends ListsRecords
{
    public string $model = Record::class;
}
