<?php

namespace App\Http\Requests\Backend\Instructors;

use Illuminate\Foundation\Http\FormRequest;

class AddNewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize()
    {
        return true;  // или реализуй проверку авторизации, если нужно
    }

    public function rules()
    {
        return [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ru' => 'nullable|string|max:255',
            'name.ka' => 'nullable|string|max:255',

            'designation' => 'required|array',
            'designation.en' => 'required|string|max:255',
            'designation.ru' => 'nullable|string|max:255',
            'designation.ka' => 'nullable|string|max:255',

            'bio' => 'nullable|array',
            'bio.en' => 'nullable|string',
            'bio.ru' => 'nullable|string',
            'bio.ka' => 'nullable|string',

            'title' => 'nullable|array',
            'title.en' => 'nullable|string|max:255',
            'title.ru' => 'nullable|string|max:255',
            'title.ka' => 'nullable|string|max:255',

            'contactNumber' => 'required|string|max:255|unique:instructors,contact',
            'emailAddress' => 'required|email|unique:instructors,email',
            'roleId' => 'required|integer|exists:roles,id',
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }
}
