<?php

namespace App\Actions\Appointment;

use App\Actions\UpdatesRecord;
use App\Models\Car;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UpdateAppointment extends UpdatesRecord
{
    public function rules(Model $record): array
    {
        $car = Car::findOrFail($record->car_id);
        $latestMileage = $car->appointments()->whereNotNull('completed_at')->orderBy('completed_at', 'desc')->first()->current_mileage ?? 1;

        return [
            'car_id' => ['nullable', 'exists:cars,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'current_mileage' => ['nullable', 'numeric', "min:$latestMileage"],
            'mileage_type' => ['nullable', 'boolean'],
            'transaction_hash' => ['nullable', 'string', 'max:255'],
        ];
    }
}
