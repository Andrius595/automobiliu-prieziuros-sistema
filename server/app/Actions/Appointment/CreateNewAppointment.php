<?php

namespace App\Actions\Appointment;

use App\Actions\CreatesNewRecord;
use App\Models\Appointment;
use App\Models\Car;
use Illuminate\Support\Facades\Validator;

class CreateNewAppointment implements CreatesNewRecord
{
    public function create(array $input): Appointment
    {
        $this->validate($input);

        return Appointment::create($input);
    }

    public function validate($input)
    {
        $car = Car::findOrFail($input['car_id']);
        // TODO what if mileage type changes?
        $latestMileage = $car->appointments()->whereNotNull('completed_at')->orderBy('completed_at', 'desc')->first()->current_mileage ?? 0;

        Validator::make($input, [
            'car_id' => ['required', 'exists:cars,id'],
            'service_id' => ['required', 'exists:services,id'],
            'current_mileage' => ['required', 'numeric', "min:$latestMileage", 'gt:0'],
            'mileage_type' => ['required', 'boolean'],
        ])->validate();
    }
}
