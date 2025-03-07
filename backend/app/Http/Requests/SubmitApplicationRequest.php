<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitApplicationRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ensure proper authentication checks if needed
    }

    public function rules()
    {
        return [
            'cover_letter' => 'required|string|max:2000',
            'rate' => 'required|numeric|min:1|max:10000',
        ];
    }
}
