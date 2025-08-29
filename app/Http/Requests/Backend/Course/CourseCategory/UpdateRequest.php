<?php

namespace App\Http\Requests\Backend\Course\CourseCategory;

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
        'category_name.en' => 'required|string|max:255',
        'category_name.ru' => 'required|string|max:255',
        'category_name.ka' => 'required|string|max:255',
        'category_status' => 'required|in:0,1',
        'category_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ];
}


}
