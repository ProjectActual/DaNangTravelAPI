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
            'uri_category'  => $model->uri_category,
            'created_at'    => $model->created_at,
        ];
    }
}
