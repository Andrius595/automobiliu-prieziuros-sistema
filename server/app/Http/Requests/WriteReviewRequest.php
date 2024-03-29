<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WriteReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');
        $userId = $this->user()->id;
        $ownerId = $appointment->car->owner_id;

        return $userId === $ownerId;
    }


    public function rules(): array
    {
        return [
            'rating' => 'required|integer|between:1,10',
            'comment' => 'nullable|string',
        ];
    }
}
