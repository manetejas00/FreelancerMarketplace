<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveFreelancerProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ensure authorization logic is applied elsewhere if needed
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'skills' => 'required|string',
            'experience' => 'nullable|integer|min:0|max:100',
            'portfolio' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'company_name' => 'nullable|string|max:255',
            'project_details' => 'nullable|string',
            'working_developers_count' => 'nullable|integer|min:0',
        ];
    }
}
