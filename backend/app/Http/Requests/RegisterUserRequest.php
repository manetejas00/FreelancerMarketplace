<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'role' => 'required|in:client,freelancer',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'This email is already taken.',
            'password.regex' => 'Password must include at least one uppercase letter and one number.',
        ];
    }
}
