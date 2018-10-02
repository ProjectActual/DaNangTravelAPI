<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Post;

/**
 * Class PostTransformer.
 *
 * @package namespace App\Transformers;
 */
class PostTransformer extends TransformerAbstract
{
    /**
     * Transform the Post entity.
     *
     * @param \App\Entities\Post $model
     *
     * @return array
     */
    public function transform(Post $model)
    {
        return [
            'id'            => (int) $model->id,
            'title'         => $model->title,
            'content'       => $model->content,
            'uri_post'      => $model->uri_post,
            'count_view'    => $model->count_view,
            'avatar_post'   => $model->avatar_post,
            'uri_category'  => $model->category->uri_category,
            'type_category' => $model->category->type_category,
            'summary'       => $model->summary,
            'created_at'    => $model->created_at,
            'updated_at'    => $model->updated_at
        ];
    }
}
