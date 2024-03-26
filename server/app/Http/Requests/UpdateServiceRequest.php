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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }
}
