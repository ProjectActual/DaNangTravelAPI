<?php

namespace App\Criteria\Category;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

use App\Entities\Category;

/**
 * Class FilterByCategoryActiveCriteria.
 *
 * @package namespace App\Criteria\Category;
 */
class FilterByCategoryActiveCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('status', Category::STATUS[1]);
    }
}
