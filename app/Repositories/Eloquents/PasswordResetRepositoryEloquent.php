<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\PasswordResetRepository;
use App\Entities\PasswordReset;
use App\Validators\PasswordResetValidator;

/**
 * Class PasswordResetRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquents;
 */
class PasswordResetRepositoryEloquent extends BaseRepository implements PasswordResetRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PasswordReset::class;
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
        return $this->model->where('email', $email)->first();
    }

    public function findByToken($token)
    {
        return $this->model->where('token', $token)->first();
    }

    public function findByEmailToken($token, $email)
    {
        return $this->model
            ->where('token', $token)
            ->where('email', $email)
            ->first();
    }
}
