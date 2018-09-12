<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\UrlRepository;
use App\Entities\Url;
use App\Validators\UrlValidator;

/**
 * Class UrlRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquents;
 */
class UrlRepositoryEloquent extends BaseRepository implements UrlRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Url::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
