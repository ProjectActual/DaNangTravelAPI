<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Feedback;
use App\Entities\User;
use App\Entities\Role;

/**
 * Class FeedbackTransformer.
 *
 * @package namespace App\Transformers;
 */
class FeedbackTransformer extends TransformerAbstract
{
    /**
     * Transform the Feedback entity.
     *
     * @param \App\Entities\Feedback $model
     *
     * @return array
     */
    public function transform(Feedback $model)
    {
        $seeder = User::where('email', $model->email)->first();

        if(empty($seeder)) {
            $seeder = "Khách vãng lai";
        } else if($seeder->hasRole(Role::NAME[2])){
            $seeder = "Cộng Tác Viên";
        } else {
            $seeder = "Quản Trị Viên";
        }

        return [
            'id'         => (int) $model->id,
            'email'      => $model->email,
            'sender'     => $seeder,
            'title'      => $model->title,
            'content'    => $model->content,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
