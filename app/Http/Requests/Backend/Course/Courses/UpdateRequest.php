<?php

namespace App\Http\Requests\Backend\Course\Courses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
           'title' => 'required|array',

        'title.en' => 'required|string|max:255',
        'title.ru' => 'required|string|max:255',
        'title.ka' => 'required|string|max:255',

        'description' => 'required|array',
        'description.en' => 'nullable|string',
        'description.ru' => 'nullable|string',
        'description.ka' => 'nullable|string',

        'prerequisites' => 'required|array',
        'prerequisites.en' => 'nullable|string',
        'prerequisites.ru' => 'nullable|string',
        'prerequisites.ka' => 'nullable|string',

        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }
}
