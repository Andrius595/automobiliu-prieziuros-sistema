<?php

namespace App\Actions\Reminder;

use App\Actions\CreatesNewRecord;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class CreateNewReminder extends CreatesNewRecord
{
    public string $model = Reminder::class;

    public function create(array $input): bool
    {
        if (!isset($input['last_reminded_at'])) {
            $input['last_reminded_at'] = now();
        }

        return parent::create($input);
    }

    public function rules($input): array
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
            'last_reminded_at' => ['nullable', 'date'],
        ];
    }
}
