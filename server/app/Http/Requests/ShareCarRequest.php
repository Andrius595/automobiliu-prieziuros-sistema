<?php

namespace App\Http\Requests;

use App\Config\PermissionsConfig;
use Illuminate\Foundation\Http\FormRequest;

class ShareCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        $car = $this->route('car');
        $carBelongsToUser = $car->belongsToUserById(auth()->id());
        $userIsAdmin = auth()->user()?->hasRole(PermissionsConfig::SYSTEM_ADMIN_ROLE);

        return $carBelongsToUser || $userIsAdmin;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }
}
