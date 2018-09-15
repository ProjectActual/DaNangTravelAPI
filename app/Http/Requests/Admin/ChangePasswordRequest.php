<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'old_password'              => 'required|min:6|max:16',
            'new_password'              => 'required|min:6|max:16|different:old_password|confirmed',
            'new_password_confirmation' => 'required|min:6|max:16',
        ];
    }
}
