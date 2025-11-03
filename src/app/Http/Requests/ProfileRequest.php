<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png',
            'birth_year' => 'nullable|integer|min:1900|max:' . now()->year,
            'birth_month' => 'nullable|integer|between:1,12',
            'birth_day' => 'nullable|integer|between:1,31',
        ];
    }
}
