<?php

namespace App\Http\Controllers\Viewer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Entities\Post;
use App\Presenters\PostPresenter;
use App\Presenters\CategoryPresenter;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\PostRepository;
use App\Repositories\Contracts\CategoryRepository;

class HomeController extends Controller
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
    }

    public function master(Request $request)
    {
        $composerFoods      = $this->post->findInMonth(Post::CODE_CATEGORY['AM_THUC']);

        $composerTravels    = $this->post->findInMonth(Post::CODE_CATEGORY['DU_LICH']);

        $composerEvents     = $this->post->findInMonth(Post::CODE_CATEGORY['SU_KIEN']);

        $composerTags       = $this->tag->get();

        $composerCategories = $this->category->get();

        return response()->json(compact('composerFoods', 'composerTravels', 'composerEvents', 'composerTags', 'composerCategories'));
    }

    public function index(Request $request)
    {
        $sliders = $this->post->findByIsSlider();

        $hots    = $this->post->findByIsHot();

        $posts    = $this->post
            ->scopeQuery(function ($query) {
                    return $query->limit(10);
            })->get();

        $foods   = $this->post->findByCategory(Post::CODE_CATEGORY['AM_THUC']);

        return response()->json(compact('sliders', 'hots', 'posts', 'foods'), 200);
    }
}
