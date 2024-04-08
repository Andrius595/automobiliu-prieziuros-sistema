<?php

namespace App\Actions\Reminder;

use App\Actions\UpdatesRecord;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Model;

class UpdateReminder extends UpdatesRecord
{
    public function rules(Model $record): array
    {
        return [
            'car_id' => ['required', 'exists:cars,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'interval' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'integer', 'in:'.
                Reminder::TYPE_MILEAGE.','.
                Reminder::TYPE_DAYS.','.
                Reminder::TYPE_WEEKS. ','.
                Reminder::TYPE_MONTHS. ','.
                Reminder::TYPE_YEARS],
            'last_reminded_at' => ['sometimes', 'date'],
        ];
    }
}
