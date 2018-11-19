<?php

namespace App\Http\Controllers\Viewer;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\BaseController;

use App\Entities\Post;
use App\Presenters\PostPresenter;
use App\Presenters\CategoryPresenter;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\PostRepository;
use App\Criteria\Post\FilterByPostActiveCriteria;
use App\Repositories\Contracts\CategoryRepository;
use App\Criteria\Category\FilterByCategoryActiveCriteria;

class HomeController extends BaseController
{
    protected $post;
    protected $category;

    public function __construct(
        TagRepository $tag,
        PostRepository $post,
        CategoryRepository $category
    ){
        $this->tag      = $tag;
        $this->post     = $post;
        $this->category = $category;

        $this->post->setPresenter(PostPresenter::class);
        $this->post->pushCriteria(FilterByPostActiveCriteria::class);
        $this->category->setPresenter(CategoryPresenter::class);
        $this->category->pushCriteria(FilterByCategoryActiveCriteria::class);
    }

    /**
     * Dùng để hiển thị theo tháng của từng danh mục trong trang homepage
     *
     * @return Illuminate\Http\Response
     */
    public function master(Request $request)
    {
        $composerFoods      = $this->post->with(['category', 'tags'])->latest()->findInMonth(Post::CODE_CATEGORY['AM_THUC']);

        $composerTravels    = $this->post->with(['category', 'tags'])->latest()->findInMonth(Post::CODE_CATEGORY['DIEM_DEN']);

        $composerEvents     = $this->post->with(['category', 'tags'])->latest()->findInMonth(Post::CODE_CATEGORY['SU_KIEN']);

        $composerTags       = $this->tag->withCount(['posts'])->get();

        $composerCategories = $this->category->withCount(['posts'])->get();

        return $this->responses(
            trans('notication.load.success'),
            Response::HTTP_OK,
            compact('composerFoods', 'composerTravels', 'composerEvents', 'composerTags', 'composerCategories')
        );
    }

    /**
     * Dùng để hiển thị số bài viết trong slider hoặc bài viết hot
     *
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sliders = $this->post->with(['category', 'tags'])->latest()->findByIsSlider();

        $hots    = $this->post->with(['category', 'tags'])->latest()->findByIsHot();

        $posts    = $this->post->with(['category', 'tags'])->getNewPost();

        $foods   = $this->post->with(['category', 'tags'])->findByCategory(Post::CODE_CATEGORY['AM_THUC']);

        return $this->responses(
            trans('notication.load.success'),
            Response::HTTP_OK,
            compact('sliders', 'hots', 'posts', 'foods')
        );
    }
}
