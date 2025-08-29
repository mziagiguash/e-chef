<?php

namespace App\Http\Requests\Backend\Course\Courses;

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
        'translations' => 'required|array',
        'translations.*.title' => 'required|string|max:255',
        'translations.*.description' => 'nullable|string',
        'translations.*.prerequisites' => 'nullable|string',
        'translations.*.keywords' => 'nullable|string',

        'course_category_id' => 'required|exists:course_categories,id',
        'instructor_id' => 'required|exists:instructors,id',
        'courseType' => 'required|in:free,paid,subscription',
        'coursePrice' => 'required|numeric|min:0',
        'start_from' => 'required|date',
        'duration' => 'required|integer|min:1',
        'lesson' => 'required|integer|min:1',
        'course_code' => 'required|string',
        'image' => 'required|image|max:2048',
        'thumbnail_image' => 'nullable|image|max:2048',
    ];
}

}
