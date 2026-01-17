<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'first_name'=>'sometimes|required|string|max:255',
            'last_name'=>'sometimes|required|string|max:255',
            'date_of_birth'=>'sometimes|required|date',
            'profile_image' => 'sometimes|required|image|mimes:webp,jpg,jpeg,gif|max:10000',
            'identity_image' => 'sometimes|required|image|mimes:webp,jpg,jpeg,gif|max:10000',
        ];
    }
}
 