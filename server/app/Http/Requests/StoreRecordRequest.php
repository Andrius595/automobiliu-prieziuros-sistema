<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRecordRequest extends FormRequest
{

    public function authorize(): bool
    {
        $user = Auth::user();
        $appointment = $this->route('appointment');

        return !(!$appointment->can_be_modified || $user?->service_id !== $appointment->service_id);
    }

    public function rules(): array
    {
        return [
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
