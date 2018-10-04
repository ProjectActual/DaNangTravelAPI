<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
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
            "name_category"     => "required|min:6|string|max:255",
            "uri_category"      => "required|min:6|regex:/[a-z0-9\-]+/|unique:urls,uri",
            "type_category"     => "required|unique:categories,type_category",
        ];
    }
}
