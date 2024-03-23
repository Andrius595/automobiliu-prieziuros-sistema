<?php

namespace App\Http\Requests;

use App\Config\PermissionsConfig;
use Illuminate\Foundation\Http\FormRequest;

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
            'make' => 'required|string',
            'model' => 'required|string',
            'year_of_manufacture' => 'required|integer:min:1900|max:' . (now()->addYear()->format('Y')),
            'mileage_type' => 'required|integer|in:0,1',
            'plate_no' => 'required|string',
        ];
    }
}
