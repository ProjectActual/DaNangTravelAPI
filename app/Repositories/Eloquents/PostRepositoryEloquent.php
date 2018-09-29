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
            ->orderBy('updated_at', 'desc');
    }

    public function oldest()
    {
        return $this->model
            ->orderBy('updated_at', 'asc');
    }

    public function searchWithPost($search)
    {
        return $this->model
            ->where(function ($query) use ($search) {
                $query
                ->orWhere('title', 'LIKE', "%{$search}%");
            });
    }

    public function findByIsSlider()
    {
        return $this->model
            ->where('is_slider', Post::IS_SLIDER['YES'])
            ->get();
    }

    public function findByIsHot()
    {
        return $this->model
            ->where('is_hot', Post::IS_HOT['YES'])
            ->get();
    }

    public function findByCategory($id)
    {
        $model = $this->model
            ->where('category_id', $id)
            ->limit(5)
            ->orderBy('created_at', 'desc')
            ->get();

        return $model;
    }
}
