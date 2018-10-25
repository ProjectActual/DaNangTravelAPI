<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\CategoryRepository;
use App\Entities\Category;
use App\Validators\CategoryValidator;

/**
 * Class CategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquents;
 */
class CategoryRepositoryEloquent extends BaseRepository implements CategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Category::class;
    }

    public function presenter()
    {
        return 'App\\Presenters\\CategoryPresenter';
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * tìm danh mục theo uri của danh mục
     *
     * @param string $uri_category đây là liên kết của danh mục
     * @return App\Entities\Category
     */
    public function findByUri($uri_category)
    {
        return $this->findByField('uri_category', $uri_category)->first();
    }

    public function findByActive()
    {
        return $this->scopeQuery(function ($query) {
            return $query->where('status', Category::STATUS[1]);
        })->get();
    }
}
