<?php

namespace App\Http\Controllers\Viewer;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Presenters\PostPresenter;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\PostRepository;
use App\Criteria\Post\FilterByPostActiveCriteria;
use App\Criteria\Tag\FilterPostByTagCriteriaCriteria;

class TagController extends BaseController
{
    protected $tag;
    protected $post;
    protected $paginate = 10;

    public function __construct(TagRepository $tag, PostRepository $post)
    {
        $this->tag  = $tag;
        $this->post = $post;
        $this->post->setPresenter(PostPresenter::class);

        $this->post->pushCriteria(FilterByPostActiveCriteria::class);
    }

    public function index(Request $request, $uri_tag)
    {
        $tag = $this->tag->findWithUri($uri_tag);
        $this->post->pushCriteria(new FilterPostByTagCriteriaCriteria($tag));
        $posts = $this->post->with('category')->paginate($this->paginate);

        return response()->json(compact('posts', 'tag'), 200);
    }
}
