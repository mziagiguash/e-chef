<?php

namespace App\Http\Requests\Backend\Course\CourseCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

public function rules(): array
{
    return [
        'category_status' => 'required|in:1,2',
        'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'translations.en.category_name' => 'required|string|max:255',
        'translations.ru.category_name' => 'required|string|max:255',
        'translations.ka.category_name' => 'required|string|max:255',
    ];
}

}
