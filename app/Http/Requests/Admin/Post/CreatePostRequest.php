<?php

namespace App\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
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
            'uri_post'      => 'required|min:6|regex:/[a-z0-9\-]+/|unique:urls,uri',
            'content'       => 'required|min:6',
            'summary'       => 'required',
            'category_id'   => 'required|exists:categories,id',
            'avatar_post'   => 'required',
        ];
    }
}
