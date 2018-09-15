<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\PostRepository;
use App\Entities\Post;
use App\Validators\PostValidator;

/**
 * Class PostRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquents;
 */
class PostRepositoryEloquent extends BaseRepository implements PostRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Post::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function latest()
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc');
    }

    public function oldest()
    {
        return $this->model
            ->orderBy('created_at', 'asc')
            ->orderBy('updated_at', 'asc');
    }
}
