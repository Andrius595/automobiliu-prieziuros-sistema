<?php

namespace App\Actions\ServiceCategory;

use App\Actions\UpdatesRecord;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Model;

class UpdateServiceCategory extends UpdatesRecord
{
    public function rules(Model $record): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
