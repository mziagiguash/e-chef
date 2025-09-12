<?php

namespace App\Http\Requests\Backend\Course\Courses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
      public function authorize(): bool
    {
        return true;
    }

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
            'courseOldPrice' => 'nullable|numeric|min:0',
            'subscription_price' => 'nullable|numeric|min:0',
            'start_from' => 'required|date',
            'duration' => 'required|integer|min:1',
            'lesson' => 'required|integer|min:1',
            'course_code' => 'required|string|max:50',
            'thumbnail_video_url' => 'nullable|url',
            'tag' => 'nullable|in:popular,featured,upcoming',
            'status' => 'required|in:0,1,2',
            'image' => 'sometimes|image|max:2048',
            'thumbnail_image' => 'nullable|image|max:2048',
            'thumbnail_video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'translations.required' => 'Translations are required',
            'translations.*.title.required' => 'Title is required for all languages',
            'course_category_id.required' => 'Category is required',
            'instructor_id.required' => 'Instructor is required',
        ];
    }

    public function attributes(): array
    {
        return [
            'translations.en.title' => 'English title',
            'translations.ru.title' => 'Russian title',
            'translations.ka.title' => 'Georgian title',
        ];
    }
}
