<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordUserRequest extends FormRequest
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
            'password'              => 'required|min:6|max:16|confirmed|string|regex:/[a-z1-9]+/',
            'password_confirmation' => 'required|min:6|max:16|string|regex:/[a-z1-9]+/',
        ];
    }
}
