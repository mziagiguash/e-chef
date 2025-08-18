<?php

namespace App\Http\Requests\Backend\Instructors;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
   public function authorize()
    {
        return true; // по желанию, добавь проверку
    }

    public function rules()
    {
        $id = $this->route('instructor'); // id из роута (шифрованный)

        // Расшифровываем, если нужно, иначе просто $id
        // $id = decrypt($id); // если используешь шифрование в роутинге

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

            // При обновлении игнорируем уникальность для текущей записи
            'contactNumber' => 'required|string|max:255|unique:instructors,contact,' . $id,
            'emailAddress' => 'required|email|unique:instructors,email,' . $id,
            'roleId' => 'required|integer|exists:roles,id',
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }
}
