<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\PostRepository;
use App\Entities\Post;
use App\Validators\PostValidator;

use Carbon\Carbon;
use App\Presenters\PostPresenter;

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
        return $this->orderBy('updated_at', 'desc');
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

    public function findByCategory($category_id)
    {
        $model = $this->model
            ->where('category_id', $category_id)
            ->limit(5)
            ->orderBy('created_at', 'desc')
            ->get();

        return $model;
    }

    public function findInMonth($category_id)
    {
        $now    = Carbon::now();

        $repository = $this->scopeQuery(function ($query) use ($now, $category_id) {
            return $query
                ->where('category_id', $category_id)
                ->orderBy('count_view', 'desc')
                ->WhereMonth('created_at', $now->month)
                ->WhereYear('created_at',  $now->year)
                ->limit(5);
            })->get();

        if(empty($repository['data'])) {
            $repository = $this->scopeQuery(function ($query) use($category_id) {
                return $query
                    ->where('category_id', $category_id)
                    ->orderBy('count_view', 'desc')
                    ->limit(5);
            })->get();
        }

        return $repository;
    }

/**
 * [filterByUrlCategory description]
 * @param  instance App\Entities\Category
 * @return category_id
 */
    public function filterByUrlCategory($category_id)
    {
        return $this->scopeQuery(function ($query) use ($category_id){
            return $query->where('category_id', $category_id);
        });
    }
}
