<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\User;

/**
 * Class UserTransformer.
 *
 * @package namespace App\Transformers;
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * Transform the User entity.
     *
     * @param \App\Entities\User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return [
            'id'                 => (int) $model->id,
            'email'              => $model->email,
            'full_name'          => $model->full_name,
            'first_name'         => $model->first_name,
            'last_name'          => $model->last_name,
            'phone'              => $model->phone,
            'avatar'             => $model->avatar,
            'gender'             => $model->gender,
            'birthday'           => $model->birthday,
            'active'             => $model->active,
            'count_posts'        => $model->count_posts ? $model->count_posts : $model->posts->count(),
            'viewer_interactive' => $model->viewer_interactive,
            'roles'              => $model->roles,
            'created_at'         => $model->created_at,
            'updated_at'         => $model->updated_at
        ];
    }
}
