<?php

namespace App\Actions\Car;

use App\Actions\UpdatesRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UpdateCar extends UpdatesRecord
{
    public function update(Model $record, array $data): bool
    {
        if (isset($data['owner_id']) && $record->owner_id !== $data['owner_id']) {
            $data['owner_confirmed_at'] = null;
        }

        return parent::update($record, $data);
    }
    public function rules(Model $record): array
    {
        return [
            'make' => ['sometimes', 'string', 'max:255'],
            'model' => ['sometimes', 'string', 'max:255'],
            'year_of_manufacture' => ['sometimes', 'integer', 'min:1900', 'max:' . (now()->addYear()->format('Y'))],
            'mileage_type' => ['sometimes', 'integer', 'in:0,1'],
            'vin' => ['sometimes', 'string', 'max:255', Rule::unique('cars')->ignore($record->id)],
            'plate_no' => ['sometimes', 'string', 'max:255'],
            'color' => ['sometimes', 'string', 'max:255'],
            'owner_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'owner_confirmed_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
