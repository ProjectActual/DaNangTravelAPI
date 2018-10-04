<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Category;

/**
 * Class CategoryTransformer.
 *
 * @package namespace App\Transformers;
 */
class CategoryTransformer extends TransformerAbstract
{
    /**
     * Transform the Category entity.
     *
     * @param \App\Entities\Category $model
     *
     * @return array
     */
    public function transform(Category $model)
    {
        return [
            'id'            => (int) $model->id,
            'name_category' => $model->name_category,
            'type_category' => $model->type_category,
            'uri_category'  => $model->uri_category,
            'description'   => $model->description,

            'count_posts'   => $model->posts->count(),
        ];
    }
}
