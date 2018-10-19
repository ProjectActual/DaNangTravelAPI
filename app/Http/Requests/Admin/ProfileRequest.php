<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

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
        $before = Carbon::create(Carbon::now()->year - 10)
            ->endOfYear()
            ->format('Y-m-d');
        $after = Carbon::create(Carbon::now()->year - 80)
            ->startOfYear()
            ->format('Y-m-d');
        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'required|string|regex:/^\+[0-9]/|max:13',
            'last_name'  => 'required|string|max:255',
            'gender'     => 'required|in:MALE,FEMALE',
            'birthday'   => "required|date|date_format:Y-m-d|before:{$before}|after:{$after}"
        ];
    }
}
