<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\PostRepository;
use App\Entities\Post;
use App\Validators\PostValidator;

use Carbon\Carbon;

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
    protected $fieldSearchable = [
        'title' => 'like',
    ];

    public function model()
    {
        return Post::class;
    }

    public function presenter()
    {
        return "App\\Presenters\\PostPresenter";
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

/**
 * sắp xếp giảm dần với updated
 * @return mixed
 */
    public function latest()
    {
        return $this
            ->orderBy('updated_at', 'desc');
    }

    /**
     * sắp xếp tăng dần với updted
     * @return mixed
     */
    public function oldest()
    {
        return $this
            ->orderBy('updated_at', 'asc');
    }

    /**
     * findByIsSlider  lọc ra những bài viết là slider
     * @return Array App\Entities\Post
     */
    public function findByIsSlider()
    {
        return $this->scopeQuery(function ($query) {
            return $query->where('is_slider', Post::IS_SLIDER['YES']);
        })->get();
    }

    /**
     * findByIsHot  lọc ra những bài viết là Hot
     * @return Array App\Entities\Post
     */
    public function findByIsHot()
    {
        return $this->scopeQuery(function ($query) {
            return $query->where('is_hot', Post::IS_HOT['YES']);
        })->get();
    }

    /**
     * findByCategory lọc bài viết theo từng danh mục
     * @param  int $category_id  đây là id của danh mục dùng để lọc
     * @return Array App\Entities\Post
     */
    public function findByCategory($category_id)
    {
        $model = $this->scopeQuery(function ($query) use ($category_id) {
            return $query
                ->where('category_id', $category_id)
                ->limit(5);
            })->latest()
            ->get();

        return $model;
    }

    /**
     * findInMonth lọc bài viết có lượt view cao nhất trong tháng, nếu như trong tháng không
     * có bài viết nào thì sẽ show 5 bài viết có lượt view cao nhất
     *
     * @param  int $category_id  đây là id của danh mục dùng để lọc
     * @return Array App\Entities\Post
     */
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
     * filterByUrlCategory  lọc theo Category id
     * @param  int $category_id  đây là id của danh mục dùng để lọc
     * @return mixed
     */
    public function filterByCategory($category_id)
    {
        return $this->scopeQuery(function ($query) use ($category_id){
            return $query->where('category_id', $category_id);
        });
    }

    /**
     * findByUri tìm theo uri bài viết
     * @param  int $uri_post đây là uri của bài viết
     * @return PostRepository
     */
    public function findByUri($uri_post)
    {
        return $this->scopeQuery(function ($query) use ($uri_post) {
                return $query
                    ->where('uri_post', $uri_post);
            })->first();
    }

    /**
     * findByUri tìm theo uri bài viết
     * @param  int $uri_post đây là uri của bài viết
     * @return PostRepository
     */
    public function filterByRelationPost($uri_post)
    {
        return $this
            ->scopeQuery(function ($query) use ($uri_post) {
                return $query
                    ->limit(4)
                    ->where('uri_post', '<>', $uri_post);
            });
    }

    /**
     * [updateAndDetachTag description]
     * @param  array  $postCredentials  mảng xác thực
     * @param  int $id              id của bài viết cần update
     * @return App\Entities\Post
     */
    public function updateAndDetachTag(array $postCredentials, $id)
    {
        $repository = $this->update($postCredentials, $id);
        $repository->tags()->detach();

        return $repository;
    }
}
