<?php

namespace App\Http\Controllers\Viewer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Entities\Post;
use App\Repositories\Contracts\PostRepository;
use App\Repositories\Contracts\CategoryRepository;

class HomeController extends Controller
{
    protected $post;
    protected $category;

    public function __construct(
        PostRepository $post,
        CategoryRepository $category
    ){
        $this->post         = $post;
        $this->category     = $category;
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
