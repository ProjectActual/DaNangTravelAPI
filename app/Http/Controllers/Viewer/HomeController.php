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
        $this->category->setPresenter(CategoryPresenter::class);
        $this->post->pushCriteria(FilterByPostActiveCriteria::class);
    }

    public function master(Request $request)
    {
        $composerFoods      = $this->post->latest()->findInMonth(Post::CODE_CATEGORY['AM_THUC']);

        $composerTravels    = $this->post->latest()->findInMonth(Post::CODE_CATEGORY['DU_LICH']);

        $composerEvents     = $this->post->latest()->findInMonth(Post::CODE_CATEGORY['SU_KIEN']);

        $composerTags       = $this->tag->get();

        $composerCategories = $this->category->get();

        return $this->responses(
            trans('notication.load.success'),
            Response::HTTP_OK,
            compact('composerFoods', 'composerTravels', 'composerEvents', 'composerTags', 'composerCategories')
        );
    }

    public function index(Request $request)
    {
        $sliders = $this->post->latest()->findByIsSlider();

        $hots    = $this->post->latest()->findByIsHot();

        $posts    = $this->post
            ->scopeQuery(function ($query) {
                    return $query->limit(10);
            })->get();

        $foods   = $this->post->findByCategory(Post::CODE_CATEGORY['AM_THUC']);

        return $this->responses(
            trans('notication.load.success'),
            Response::HTTP_OK,
            compact('sliders', 'hots', 'posts', 'foods')
        );
    }
}
