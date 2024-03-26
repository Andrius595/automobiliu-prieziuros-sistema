<?php

namespace App\Actions\Service;

use App\Actions\UpdatesRecord;
use App\Config\PermissionsConfig;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UpdateService extends UpdatesRecord
{
    public function rules(Model $record): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }
}
