<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveCarFromUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('car')?->belongsToUserById($this->user()->id);
    }
}
