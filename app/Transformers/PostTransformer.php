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
        $tags = $model->tags->map(function ($tag) {
            return $tag->only(['id', 'tag', 'uri_tag']);
        });
        return [
            'id'            => (int) $model->id,
            'title'         => $model->title,
            'content'       => $model->content,
            'uri_post'      => $model->uri_post,
            'count_view'    => $model->count_view,
            'avatar_post'   => $model->avatar_post,
            'summary'       => $model->summary,
            'status'        => $model->status,
            'is_slider'     => $model->is_slider,
            'is_hot'        => $model->is_hot,

            'category_id'   => $model->category_id,
            'uri_category'  => $model->category->uri_category,
            'type_category' => $model->category->type_category,

            'tag'           => $tags,
            'created_at'    => $model->created_at->toDateTimeString(),
            'updated_at'    => $model->updated_at->toDateTimeString()
        ];
    }
}
