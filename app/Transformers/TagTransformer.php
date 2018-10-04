<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Tag;

/**
 * Class TagTransformer.
 *
 * @package namespace App\Transformers;
 */
class TagTransformer extends TransformerAbstract
{
    /**
     * Transform the Tag entity.
     *
     * @param \App\Entities\Tag $model
     *
     * @return array
     */
    public function transform(Tag $model)
    {
        return [
            'id'          => (int) $model->id,
            'tag'         => $model->tag,
            'uri_tag'     => $model->uri_tag,
            'created_at'     => $model->created_at,
            'count_posts' => $model->posts->count(),
        ];
    }
}
