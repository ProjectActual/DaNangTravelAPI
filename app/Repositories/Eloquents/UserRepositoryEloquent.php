<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\UserRepository;

use App\Entities\User;
use App\Entities\Role;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquents;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $fieldSearchable = [
        'first_name' => 'like',
        'last_name'  => 'like',
    ];

    public function model()
    {
        return User::class;
    }

    public function presenter()
    {
        return "App\\Presenters\\UserPresenter";
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findByEmail($email)
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }

    public function sortByCTV()
    {
        return $this->scopeQuery(function ($query) {
            return $query
                ->orderBy('created_at', 'desc');
        });
    }

    public function searchWithActive($status)
    {
        return $this->scopeQuery(function ($query) use ($status) {
            return $query
                ->where(function ($query) use ($status) {
                    $query->orWhere('active','LIKE' , "%$status%");
                });
        });
    }
}
