<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

use App\Entities\Role;

/**
 * Class FilterByCongTacVienCriteria.
 *
 * @package namespace App\Criteria;
 */
class FilterByCongTacVienCriteria implements CriteriaInterface
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
        return $model
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id', Role::CODE_NAME['CONGTACVIEN']);
    }
}
