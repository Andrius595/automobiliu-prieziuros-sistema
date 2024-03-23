<?php

namespace App\Actions\Record;

use App\Actions\CreatesNewRecord;
use App\Models\Car;
use App\Models\Record;
use Illuminate\Support\Facades\Validator;

class CreateNewRecord extends CreatesNewRecord
{
    public function create(array $input): Record
    {
        $this->validate($input);

        return Record::create($input);
    }

    public function rules($input): array
    {
        return [
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'appointment_id' => ['required', 'exists:appointments,id'],
        ];
    }
}
