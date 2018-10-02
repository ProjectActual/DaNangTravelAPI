<?php

namespace App\Http\Controllers\Viewer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Presenters\PostPresenter;
use App\Repositories\Contracts\PostRepository;
use App\Repositories\Contracts\CategoryRepository;

class PostController extends Controller
{
    protected $post;
    protected $category;
    protected $paginate = 7;

    public function __construct(
        PostRepository $post,
        CategoryRepository $category
    ){
        $this->category = $category;
        $this->post     = $post;
        $this->post->setPresenter(PostPresenter::class);
    }

    public function index(Request $request, $uri_category)
    {
        $category = $this->category->findByUri($uri_category);

        $posts = $this->post
            ->filterByUrlCategory($category->id)
            ->paginate($this->paginate);

        return response()->json($posts, 200);
    }
}
