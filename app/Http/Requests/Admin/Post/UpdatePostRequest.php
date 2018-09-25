<?php

namespace App\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'title'         => 'required|min:6|max:255',
            'uri_post'      => 'required|min:6|regex:/[a-z0-9\-]+/',
            'content'       => 'required|min:6',
            'status'        => 'required',
            'category_id'   => 'required|exists:categories,id',
        ];
    }
}
