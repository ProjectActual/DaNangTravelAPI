<?php

namespace App\Criteria\Feedback;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

use App\Entities\Role;

/**
 * Class FilterFeedBackWithCTVCriteria.
 *
 * @package namespace App\Criteria\Feedback;
 */
class FilterFeedBackWithCTVCriteria implements CriteriaInterface
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

        if(!$user->hasRole(Role::NAME[1])) {
            $model = $model->where('email', $user->email);
        }

        return $model;
    }
}
