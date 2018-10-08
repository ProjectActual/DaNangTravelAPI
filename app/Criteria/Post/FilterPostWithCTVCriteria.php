<?php

namespace App\Criteria\Post;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

use Entrust;
use App\Entities\Role;

/**
 * Class FilterPostWithCTVCriteria.
 *
 * @package namespace App\Criteria\Post;
 */
class FilterPostWithCTVCriteria implements CriteriaInterface
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
        $user = request()->user();

        if(Entrust::hasRole(Role::NAME[2])) {
            $model = $model->where('user_id', $user->id);
        }

        return $model;
    }
}
