<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WriteReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');

        return $appointment->car->belongsToUserById($this->user()->id);
    }


    public function rules(): array
    {
        return [
            'rating' => 'required|integer|between:1,10',
            'comment' => 'nullable|string',
        ];
    }
}
