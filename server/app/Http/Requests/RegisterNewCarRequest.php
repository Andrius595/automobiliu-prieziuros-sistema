<?php

namespace App\Http\Requests;

use App\Rules\PlateNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterNewCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vin' => ['required', 'string', 'unique:cars,vin'],
            'plate_no' => ['required', 'string', new PlateNumberRule],
            'make' => ['required', 'string'],
            'model' => ['required', 'string'],
            'year_of_manufacture' => ['sometimes', 'integer', 'min:1900', 'max:2024'],
            'color' => ['sometimes', 'string'],
            'mileage_type' => ['required', 'boolean'],
            'registration_document' => ['required', 'string'],
        ];
    }
}
