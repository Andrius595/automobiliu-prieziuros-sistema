<?php

namespace App\Actions\Service;

use App\Actions\UpdatesRecord;
use App\Config\PermissionsConfig;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UpdateEmployee extends UpdatesRecord
{
    public function update(Model $record, array $data): bool
    {
        $updateStatus = parent::update($record, $data);
        $record->syncRoles([$data['role']]);

        return $updateStatus;
    }
    public function rules(Model $record): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($record->id),
            ],
            'role' => ['required', Rule::in([PermissionsConfig::SERVICE_EMPLOYEE_ROLE, PermissionsConfig::SERVICE_ADMIN_ROLE])]
        ];
    }
}
