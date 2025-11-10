<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IllegalObjectUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'object_id' => 'required|exists:illegal_objects,id',
            'files' => 'required|array',
        ];
    }
}
