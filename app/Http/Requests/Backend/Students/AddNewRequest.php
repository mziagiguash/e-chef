<?php

namespace App\Http\Requests\Backend\Students;

use Illuminate\Foundation\Http\FormRequest;

class AddNewRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => 'required|unique:instructors,email',
            'contact' => 'required|unique:instructors,contact',
            'role_id' => 'required|max:3',
            'password' => 'required',
        ];
    }
}
