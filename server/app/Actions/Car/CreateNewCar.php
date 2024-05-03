<?php

namespace App\Actions\Car;

use App\Actions\CreatesNewRecord;
use App\Models\Car;
use App\Rules\PlateNumberRule;
use Illuminate\Support\Facades\Validator;

class CreateNewCar extends CreatesNewRecord
{
    public function create(array $input): Car
    {
        $this->validate($input);

        $car = Car::create($input);

        if ($input['owner_id']) {
            $car->users()->attach($input['owner_id']);
        }

        return $car;
    }

    public function rules($input): array
    {
        return [
            'vin' => ['required', 'string', 'unique:cars,vin'],
            'plate_no' => ['required', 'string', new PlateNumberRule],
            'make' => ['required', 'string'],
            'model' => ['required', 'string'],
            'year_of_manufacture' => ['sometimes', 'integer', 'min:1900', 'max:2024'],
            'color' => ['sometimes', 'string'],
            'mileage_type' => ['required', 'boolean'],
            'owner_id' => ['sometimes', 'integer', 'exists:users,id'],
            'registration_document' => ['sometimes', 'string'],
        ];
    }
}
