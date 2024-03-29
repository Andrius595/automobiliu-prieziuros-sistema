<?php

namespace App\Http\Requests;

use App\Config\PermissionsConfig;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->hasRole(PermissionsConfig::SYSTEM_ADMIN_ROLE) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
        ];
    }
}
