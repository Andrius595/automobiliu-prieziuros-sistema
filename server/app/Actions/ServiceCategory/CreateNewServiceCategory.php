<?php

namespace App\Actions\ServiceCategory;

use App\Actions\CreatesNewRecord;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\City;
use App\Models\Reminder;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class CreateNewServiceCategory extends CreatesNewRecord
{
    public string $model = ServiceCategory::class;

    public function rules($input): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
