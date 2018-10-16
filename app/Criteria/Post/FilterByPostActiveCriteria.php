<?php

namespace App\Criteria\Post;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

use App\Entities\Post;

/**
 * Class FilterByPostActiveCriteria.
 *
 * @package namespace App\Criteria\Post;
 */
class FilterByPostActiveCriteria implements CriteriaInterface
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
        return $model->where('status', Post::STATUS['ACTIVE']);
    }
}
