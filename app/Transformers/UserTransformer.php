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
        $roles = $model->roles->map(function ($role) {
            return $role->only(['name', 'display_name']);;
        });
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
            'count_posts'        => $model->posts_count,
            'viewer_interactive' => $model->viewer_interactive,
            'roles'              => $roles,
            'created_at'         => $model->created_at->toDateTimeString(),
            'updated_at'         => $model->updated_at->toDateTimeString()
        ];
    }
}
