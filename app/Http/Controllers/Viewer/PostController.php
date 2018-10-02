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
    protected $paginate = 10;

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

    public function show(Request $request, $uri_category, $uri_post)
    {
        $post = $this->post->with('tags')->findByUri($uri_post);

        $relationPost = $this->post->filterByRelationPost($uri_post);

        return response()->json(compact('post','relationPost'), 200);
    }
}
