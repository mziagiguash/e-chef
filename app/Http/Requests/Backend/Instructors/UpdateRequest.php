<?php

namespace App\Http\Requests\Backend\Instructors;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // При необходимости добавьте логику авторизации
    }

    public function rules()
    {
        // Получаем id инструктора из route параметров (предполагается, что он называется 'id')
        $instructorId = $this->route('id');

        return [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ru' => 'required|string|max:255',
            'name.ka' => 'required|string|max:255',

            'designation' => 'required|array',
            'designation.en' => 'required|string|max:255',
            'designation.ru' => 'required|string|max:255',
            'designation.ka' => 'required|string|max:255',

            'bio' => 'nullable|array',
            'bio.en' => 'nullable|string',
            'bio.ru' => 'nullable|string',
            'bio.ka' => 'nullable|string',

            'contactNumber' => 'required|string|max:20',

            'emailAddress' => [
                'required',
                'email',
                Rule::unique('instructors', 'email')->ignore($instructorId, 'id'),
            ],

            'roleId' => 'required|exists:roles,id',
            'title' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',

            // Пароль необязателен при обновлении, если хотите позволить менять
            // пароль только при вводе:
            'password' => 'nullable|string|min:6|confirmed',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.en.required' => 'The English name is required.',
            'name.ru.required' => 'The Russian name is required.',
            'name.ka.required' => 'The Georgian name is required.',

            'designation.en.required' => 'The English designation is required.',
            'designation.ru.required' => 'The Russian designation is required.',
            'designation.ka.required' => 'The Georgian designation is required.',

            'contactNumber.required' => 'Contact number is required.',
            'emailAddress.required' => 'Email address is required.',
            'emailAddress.email' => 'Email address must be valid.',
            'emailAddress.unique' => 'This email is already taken.',
            'roleId.required' => 'Role is required.',
            'roleId.exists' => 'Selected role does not exist.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, gif.',
            'image.max' => 'Image size must be less than 2MB.',
        ];
    }
}
