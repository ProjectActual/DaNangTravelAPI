<?php

namespace App\Repositories\Eloquents;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\PostRepository;
use App\Validators\PostValidator;
use Carbon\Carbon;

use App\Entities\Category;
use App\Entities\Post;
use App\Entities\Role;
use Entrust;

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
 *
 * @return mixed
 */
    public function latest()
    {
        return $this
            ->orderBy('created_at', 'desc');
    }

    public function order($sort='created_at', $status='desc')
    {
        return $this
            ->orderBy($sort, $status);
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
     * @return Colection
     */
    public function findByIsSlider()
    {
        return $this->scopeQuery(function ($query) {
            return $query->where('is_slider', Post::IS_SLIDER['YES']);
        })->get();
    }

    /**
     * findByIsHot  lọc ra những bài viết là Hot
     * @return Colectiont
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
     * @return Colection
     */
    public function findByCategory($category_id)
    {
        $model = $this->scopeQuery(function ($query) use ($category_id) {
            return $query
                ->where('category_id', $category_id)
                ->limit(5);
            })->latest()->get();
        return $model;
    }

    /**
     * findInMonth lọc bài viết có lượt view cao nhất trong tháng, nếu như trong tháng không
     * có bài viết nào thì sẽ show 5 bài viết có lượt view cao nhất
     *
     * @param  int $category_id  đây là id của danh mục dùng để lọc
     * @return Colection
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
            $this->resetScope();
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
     * @return Repository
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
                ->where('uri_post', '<>', $uri_post)
                ->orderBy('created_at', 'desc');
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

    /**
     * filterPostInMonthCurrent  count filter all posts in month
     * @return PostRepository
     */
    public function countPostInMonthCurrent()
    {
        $now        = Carbon::now();
        $user       = request()->user();
        if($user->hasRole(Role::NAME[1])) {
            $repository = $this->scopeQuery(function ($query) use ($now) {
                return $query
                    ->selectRaw('count(*) as countInMonth, sum(count_view) as sumInMonth')
                    ->whereYear('created_at', '=', $now->year)
                    ->whereMonth('created_at', '=', $now->month);
                })->first();
        } else{
            $repository = $this->scopeQuery(function ($query) use ($now, $user) {
                return $query
                    ->selectRaw('count(*) as countInMonth, sum(count_view) as sumInMonth')
                    ->whereYear('created_at', '=', $now->year)
                    ->whereMonth('created_at', '=', $now->month)
                    ->where('user_id', $user->id);
            })->first();
        }
        return $repository;
    }

    /**
     * countPostWithRole  count filter all posts
     *
     * @return App\Entities\Post
     */
    public function countPostWithRole()
    {
        $user       = request()->user();

        if($user->hasRole(Role::NAME[1])) {
            $repository = $this->scopeQuery(function ($query) {
                return $query
                    ->selectRaw('count(*) as count, sum(count_view) as sum');
                })->first();
        }else {
            $repository = $this->scopeQuery(function ($query) use ($user) {
                return $query
                    ->selectRaw('count(*) as count, sum(count_view) as sum')
                    ->where('user_id', $user->id);
            })->first();
        }
        return $repository;
    }

    /**
     * Total number of articles / monthly views of CTV
     *
     * @param  Carbon $dateStart time to start searching
     * @param  Carbon $dateEnd   time to end searching
     * @return Colection
     */
    public function statisticWithPostMonth($dateStart, $dateEnd)
    {
        $user = request()->user();
        //check role is ADMIN: view all, CONGTACVIEN: only himself
        if(!$user->hasRole(Role::NAME[1])) {
            $repository = $this->scopeQuery(function ($query) use ($user, $dateStart, $dateEnd) {
                return $query
                    ->selectRaw(
                        'count(posts.id) as count_posts,
                        sum(posts.count_view) as count_views,
                        month(posts.created_at) as month,
                        year(posts.created_at) as year'
                    )->where('user_id', $user->id)
                    ->whereDate('created_at', '>=', $dateStart)
                    ->whereDate('created_at', '<=', $dateEnd)
                    ->groupBy('month', 'year');
            });
        } else {
            $repository = $this->scopeQuery(function ($query) use ($user, $dateStart, $dateEnd) {
                return $query
                    ->selectRaw(
                        'count(posts.id) as count_posts,
                        sum(posts.count_view) as count_views,
                        month(posts.created_at) as month,
                        year(posts.created_at) as year'
                    )->whereDate('created_at', '>=', $dateStart)
                    ->whereDate('created_at', '<=', $dateEnd)
                    ->groupBy('month', 'year');
            });
        }
        return $repository->get();
    }

    public function getNewPost()
    {
        return $this->scopeQuery(function ($query) {
                    return $query
                        ->join('categories', 'posts.category_id', 'categories.id')
                        ->where('categories.status', Category::STATUS[1])
                        ->limit(10);
            })->get();
    }
}
