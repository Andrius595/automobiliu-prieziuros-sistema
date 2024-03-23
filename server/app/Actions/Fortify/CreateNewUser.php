<?php

namespace App\Actions\Fortify;

use App\Actions\CreatesNewRecord;
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

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
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
            'password' => $this->passwordRules(),
        ];
    }
}
