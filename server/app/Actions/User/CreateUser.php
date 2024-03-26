<?php

namespace App\Actions\User;

use App\Actions\CreatesNewRecord;
use App\Models\Car;
use App\Models\User;

class CreateUser extends CreatesNewRecord
{
    public function create(array $input): Car
    {
        $this->validate($input);

        return User::create($input);
    }

    public function rules($input): array
    {
        return [
            'vin' => ['required', 'string', 'unique:cars,vin'],
            'plate_no' => ['required', 'string'], // TODO plate rules
            'make' => ['required', 'string'],
            'model' => ['required', 'string'],
            'year_of_manufacture' => ['sometimes', 'integer', 'min:1900', 'max:2024'],
            'color' => ['sometimes', 'string'],
            'mileage_type' => ['required', 'boolean'],
            'owner_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }
}
