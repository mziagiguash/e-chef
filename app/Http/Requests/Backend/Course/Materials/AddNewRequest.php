<?php

namespace App\Http\Requests\Backend\Course\Materials;

use Illuminate\Foundation\Http\FormRequest;

class AddNewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = ['en', 'ru', 'ka']; // поддерживаемые языки

        $rules = [
            'materialType' => 'required|max:255',
            'lessonId'     => 'required|integer',
            'content'      => 'nullable|file|max:204800', // до 200MB
            'contentURL'   => 'nullable|url',
        ];

        // Правила для мультиязычного текста
        foreach ($locales as $locale) {
            $rules["content_text.$locale"] = 'nullable|string';
            $rules["materialTitle.$locale"] = 'required|string|max:255';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'lessonId.required' => 'Please select a lesson.',
            'materialType.required' => 'Please select a material type.',
            'content.max' => 'The file is too large. Maximum size is 200MB.',
        ];
    }
}
