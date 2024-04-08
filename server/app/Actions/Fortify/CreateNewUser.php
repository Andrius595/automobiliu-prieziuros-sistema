<?php

namespace App\Actions\Fortify;

use App\Actions\CreatesNewRecord;
use App\Config\PermissionsConfig;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CreateNewUser extends CreatesNewRecord
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
            'password' => $input['password'] ? Hash::make($input['password']) : 'not_set',
        ]);

        $user->assignRole($input['role'] ?? PermissionsConfig::CLIENT_ROLE);

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
            'role' => ['required', Rule::in(PermissionsConfig::ROLES)],
        ];
    }
}
