<?php

return [


// validator custom

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted'             => 'Trường :attribute must be accepted.',
    'active_url'           => 'Trường :attribute is not a valid URL.',
    'after'                => 'Trường :attribute phải sau ngày :date.',
    'after_or_equal'       => 'Trường :attribute must be a date after or equal to :date.',
    'alpha'                => 'Trường :attribute may only contain letters.',
    'alpha_dash'           => 'Trường :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num'            => 'Trường :attribute may only contain letters and numbers.',
    'array'                => 'Trường :attribute must be an array.',
    'before'               => 'Trường :attribute phải trước ngày :date.',
    'before_or_equal'      => 'Trường :attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => 'Trường :attribute must be between :min and :max.',
        'file'    => 'Trường :attribute must be between :min and :max kilobytes.',
        'string'  => 'Trường :attribute must be between :min and :max characters.',
        'array'   => 'Trường :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'Trường :attribute field must be true or false.',
    'confirmed'            => 'Trường :attribute không trùng khớp.',
    'date'                 => 'Trường :attribute is not a valid date.',
    'date_format'          => 'Trường :attribute không trùng khớp Trường format :format.',
    'different'            => 'Trường :attribute and :other must be different.',
    'digits'               => 'Trường :attribute must be :digits digits.',
    'digits_between'       => 'Trường :attribute must be between :min and :max digits.',
    'dimensions'           => 'Trường :attribute has invalid image dimensions.',
    'distinct'             => 'Trường :attribute field has a duplicate value.',
    'email'                => 'Trường :attribute must be a valid email address.',
    'exists'               => 'Trường :attribute chưa được chọn.',
    'file'                 => 'Trường :attribute must be a file.',
    'filled'               => 'Trường :attribute field must have a value.',
    'gt'                   => [
        'numeric' => 'Trường :attribute must be greater than :value.',
        'file'    => 'Trường :attribute must be greater than :value kilobytes.',
        'string'  => 'Trường :attribute must be greater than :value characters.',
        'array'   => 'Trường :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'Trường :attribute must be greater than or equal :value.',
        'file'    => 'Trường :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'Trường :attribute must be greater than or equal :value characters.',
        'array'   => 'Trường :attribute must have :value items or more.',
    ],
    'image'                => 'Trường :attribute phải là một ảnh.',
    'in'                   => 'Trường selected :attribute is invalid.',
    'in_array'             => 'Trường :attribute field does not exist in :other.',
    'integer'              => 'Trường :attribute must be an integer.',
    'ip'                   => 'Trường :attribute must be a valid IP address.',
    'ipv4'                 => 'Trường :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'Trường :attribute must be a valid IPv6 address.',
    'json'                 => 'Trường :attribute must be a valid JSON string.',
    'lt'                   => [
        'numeric' => 'Trường :attribute must be less than :value.',
        'file'    => 'Trường :attribute must be less than :value kilobytes.',
        'string'  => 'Trường :attribute must be less than :value characters.',
        'array'   => 'Trường :attribute must have less than :value items.',
    ],
    'lte'                  => [
        'numeric' => 'Trường :attribute must be less than or equal :value.',
        'file'    => 'Trường :attribute must be less than or equal :value kilobytes.',
        'string'  => 'Trường :attribute must be less than or equal :value characters.',
        'array'   => 'Trường :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => 'Trường :attribute không thể lớn hơn :max.',
        'file'    => 'Trường :attribute không thể lớn hơn :max kg.',
        'string'  => 'Trường :attribute không thể lớn hơn :max kí tự.',
        'array'   => 'Trường :attribute may not have more than :max items.',
    ],
    'mimes'                => 'Trường :attribute must be a file of type: :values.',
    'mimetypes'            => 'Trường :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'Trường :attribute không được nhỏ :min.',
        'file'    => 'Trường :attribute không được nhỏ :min kg.',
        'string'  => 'Trường :attribute không được nhỏ :min kí tự.',
        'array'   => 'Trường :attribute must have at least :min items.',
    ],
    'not_in'               => 'Trường selected :attribute is invalid.',
    'not_regex'            => 'Trường :attribute format is invalid.',
    'numeric'              => 'Trường :attribute must be a number.',
    'present'              => 'Trường :attribute field must be present.',
    'regex'                => 'Trường :attribute format is invalid.',
    'required'             => 'Trường :attribute không được trống.',
    'required_if'          => 'Trường :attribute field is required when :other is :value.',
    'required_unless'      => 'Trường :attribute field is required unless :other is in :values.',
    'required_with'        => 'Trường :attribute field is required when :values is present.',
    'required_with_all'    => 'Trường :attribute field is required when :values is present.',
    'required_without'     => 'Trường :attribute field is required when :values is not present.',
    'required_without_all' => 'Trường :attribute field is required when none of :values are present.',
    'same'                 => 'Trường :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'Trường :attribute must be :size.',
        'file'    => 'Trường :attribute must be :size kilobytes.',
        'string'  => 'Trường :attribute must be :size characters.',
        'array'   => 'Trường :attribute must contain :size items.',
    ],
    'string'               => 'Trường :attribute must be a string.',
    'timezone'             => 'Trường :attribute must be a valid zone.',
    'unique'               => 'Trường :attribute đã tồn tại trong hồ sơ dữ liệu.',
    'uploaded'             => 'Trường :attribute failed to upload.',
    'url'                  => 'Trường :attribute format is invalid.',
    'not_found'            => ':attribute không được tìm thấy trong hồ sơ dữ liệu',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'uri_post'              => 'liên kết bài viết',
        'title'                 => 'tiêu đề',
        'avatar_post'           => 'ảnh bài viết',
        'content'               => 'nội dung bài viết',
        'category_id'           => 'danh mục',
        'name_category'         => 'tên danh mục',
        'uri_category'          => 'liên kết danh mục',
        'category'              => 'danh mục',
        'password_reset'        => 'xác nhận mật khẩu',
        'first_name'            => 'Tên',
        'last_name'             => 'Họ',
        'password'              => 'mật khẩu',
        'password_confirmation' => 'xác nhận lại mật khẩu',
        'birthday'              => 'ngày sinh',
    ],

];
