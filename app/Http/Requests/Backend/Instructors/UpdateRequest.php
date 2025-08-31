<?php

namespace App\Http\Requests\Backend\Instructors;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
   public function authorize()
    {
        return true; // по желанию, добавь проверку
    }

    public function rules(): array
{
    return [
        'email'       => 'nullable|email|max:255',
        'contact'     => 'nullable|string|max:255',
        'role_id'     => 'required|exists:roles,id',
        'status'      => 'required|in:0,1',
        'access_block'=> 'nullable|in:0,1',
        'password'    => 'nullable|string|min:6',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',

        'name'           => 'array',
        'name.*'         => 'nullable|string|max:255',
        'designation'    => 'array',
        'designation.*'  => 'nullable|string|max:255',
        'title'          => 'array',
        'title.*'        => 'nullable|string|max:255',
        'bio'            => 'array',
        'bio.*'          => 'nullable|string',
    ];
}

}
