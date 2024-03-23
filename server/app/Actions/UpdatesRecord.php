<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

abstract class UpdatesRecord
{
    public function update(Model $record, array $data): bool
    {
        $this->validate($data, $record);

        return $record->update($data);
    }

    public function validate(array $data, Model $record): void
    {
        Validator::make($data, $this->rules($record))->validate();
    }

    abstract public function rules(Model $record): array;
}
