<?php

namespace App\Http\Requests;

use App\Config\PermissionsConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        if ($user) {
            $isSystemAdmin = $user->hasRole(PermissionsConfig::SYSTEM_ADMIN_ROLE);

            $isServiceAdmin = $user->hasRole(PermissionsConfig::SERVICE_ADMIN_ROLE);

            return $isSystemAdmin || ($isServiceAdmin && $user->service_id === $this->route('service')->id);
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'service_categories_ids' => ['nullable', 'array'],
            'service_categories_ids.*' => ['integer', 'exists:service_categories,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ];
    }
}
