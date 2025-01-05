<?php

namespace App\Http\Requests;

use App\Config\PermissionsConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole([PermissionsConfig::CLIENT_ROLE, PermissionsConfig::SYSTEM_ADMIN_ROLE]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'make' => ['sometimes', 'string', 'max:255'],
            'model' => ['sometimes', 'string', 'max:255'],
            'year_of_manufacture' => ['sometimes', 'integer', 'min:1900', 'max:' . (now()->addYear()->format('Y'))],
            'mileage_type' => ['sometimes', 'integer', 'in:0,1'],
            'vin' => ['sometimes', 'string', 'max:255', Rule::unique('cars')->ignore($this->car)],
            'plate_no' => ['sometimes', 'string', 'max:255'],
            'color' => ['sometimes', 'string', 'max:255'],
            'owner_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'owner_confirmed_at' => ['sometimes', 'nullable', 'date'],
            'image.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
