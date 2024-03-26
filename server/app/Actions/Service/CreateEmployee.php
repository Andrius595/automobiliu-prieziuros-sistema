<?php

namespace App\Actions\Service;

use App\Actions\CreatesNewRecord;
use App\Actions\Fortify\PasswordValidationRules;
use App\Config\PermissionsConfig;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class CreateEmployee extends CreatesNewRecord
{
    use PasswordValidationRules;

    public string $model = User::class;
    public function create(array $input): User
    {
        $this->validate($input);
        $user = User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'service_id' => $input['service_id'],
            'password' => 'not_set',
        ]);

        $user->assignRole($input['role']);

        Password::sendResetLink([
            'email' => $input['email']
        ]);

        return $user;
    }


    public function rules($input): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'role' => ['required', Rule::in([PermissionsConfig::SERVICE_EMPLOYEE_ROLE, PermissionsConfig::SERVICE_ADMIN_ROLE])]
        ];
    }
}
