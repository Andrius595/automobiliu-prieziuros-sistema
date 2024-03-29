<?php

namespace App\Actions\City;

use App\Actions\UpdatesRecord;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Model;

class UpdateCity extends UpdatesRecord
{
    public function rules(Model $record): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
