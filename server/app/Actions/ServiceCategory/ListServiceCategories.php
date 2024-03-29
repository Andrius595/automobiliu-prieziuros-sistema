<?php

namespace App\Actions\ServiceCategory;

use App\Actions\ListsRecords;
use App\Models\ServiceCategory;

class ListServiceCategories extends ListsRecords
{
    public string $model = ServiceCategory::class;
}
