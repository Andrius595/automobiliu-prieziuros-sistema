<?php

namespace App\Actions;

use Illuminate\Support\Facades\Validator;

abstract class CreatesNewRecord
{
    public string $model;

    public function create(array $input) {
        $this->validate($input);

        return $this->model::create($input);
    }

    abstract public function rules(array $input): array;

    public function validate(array $input): void
    {
        Validator::make($input, $this->rules($input))->validate();
    }
}
