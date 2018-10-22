<?php

namespace App\Criteria\CTV;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

use App\Entities\Role;

/**
 * Class StatictisWithPostCriteria.
 *
 * @package namespace App\Criteria\CTV;
 */
class StatictisWithPostCriteria implements CriteriaInterface
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }
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
        //Get posts by user and filter by month and year
        return $model
            ->selectRaw('users.*, count(posts.id) as count_posts, sum(posts.count_view) as viewer_interactive')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id', Role::CODE_NAME['CONGTACVIEN'])
            ->join('posts', 'posts.user_id', '=', 'users.id')
            ->whereMonth('posts.created_at', '=', $this->date->month)
            ->whereYear('posts.created_at', '=', $this->date->year)
            ->orderBy('count_posts', 'desc')
            ->orderBy('viewer_interactive', 'desc')
            ->groupBy('users.id');
    }
}
