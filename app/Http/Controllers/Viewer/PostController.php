<?php

namespace App\Http\Controllers\Viewer;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Presenters\PostPresenter;
use App\Http\Requests\SearchRequest;
use App\Repositories\Contracts\PostRepository;
use App\Criteria\Post\FilterByPostActiveCriteria;
use App\Repositories\Contracts\CategoryRepository;

class PostController extends BaseController
{
    protected $post;
    protected $category;
    protected $paginate = 15;

    public function __construct(
        PostRepository $post,
        CategoryRepository $category
    ){
        $this->category = $category;
        $this->post     = $post;

        $this->post->setPresenter(PostPresenter::class);
        $this->post->pushCriteria(FilterByPostActiveCriteria::class);
        $this->category->skipPresenter();
    }

    public function index(Request $request, $uri_category)
    {
        $category = $this->category->findByUri($uri_category);

        $posts    = $this->post
            ->filterByUrlCategory($category->id)
            ->latest()
            ->paginate($this->paginate);

        return response()->json($posts, 200);
    }

    public function show(Request $request, $uri_category, $uri_post)
    {
        $post         = $this->post->with('tags')->findByUri($uri_post);

        $relationPost = $this->post->filterByRelationPost($uri_post);

        return response()->json(compact('post','relationPost'), 200);
    }

    public function search(SearchRequest $request)
    {
        $posts = $this->post->paginate($this->paginate);

        return $this->responses(trans('notication.load.success'), 200, compact('posts'));
    }
}
