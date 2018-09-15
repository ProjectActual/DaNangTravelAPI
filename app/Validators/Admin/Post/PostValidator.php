<?php

namespace App\Validators\Admin\Post;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class PostValidator.
 *
 * @package namespace App\Validators\Admin\Post;
 */
class PostValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'title'         => 'required|min:6',
            'url_post'      => 'required|min:6|regex:/[a-z0-9\-]+/',
        ],
        ValidatorInterface::RULE_UPDATE => [],
    ];
}
