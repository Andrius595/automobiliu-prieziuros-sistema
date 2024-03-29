<?php

namespace App\Actions\City;

use App\Actions\CreatesNewRecord;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\City;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class CreateNewCity extends CreatesNewRecord
{
    public string $model = City::class;

    public function rules($input): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
