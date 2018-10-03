<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use Uuid;
use App\Entities\Post;
use App\Services\SendMail;
use App\Http\Controllers\BaseController;
use App\Notifications\PasswordResetRequest;
use App\Repositories\Contracts\UrlRepository;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\PostRepository;
use App\Repositories\Contracts\CategoryRepository;
use App\Http\Requests\Admin\Post\CreatePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;

class PostController extends BaseController
{
    protected $tag;
    protected $url;
    protected $post;
    protected $paginate = 15;

    public function __construct(
        TagRepository $tag,
        UrlRepository $url,
        PostRepository $post,
        CategoryRepository $category
    ){
        $this->tag        = $tag;
        $this->url         = $url;
        $this->post        = $post;
        $this->category   = $category;

        $this->tag->skipPresenter();
        $this->post->skipCriteria();
    }

    public function index(Request $request)
    {
        $posts = $this->post
            ->searchWithPost($request->search)
            ->orderBy('updated_at', 'desc')
            ->paginate($this->paginate);

        return response()->json($posts);
    }

    public function show($id)
    {
        $post = $this->post->find($id);

        return response()->json($post);
    }

    public function store(CreatePostRequest $request)
    {
        $user         = $request->user();
        $request->tag = json_decode($request->tag);

        if(count($request->tag) > 10) {
            return $this->responseErrors('tag', trans('validation.max.numeric', ['attribute' => 'tag', 'max' => 10]));
        }

        $this->url->create([
            'url_title'     => $request->title,
            'uri'           => $request->uri_post,
        ]);

        $post   = $this->post->create([
            'content'     => $request->content,
            'title'       => $request->title,
            'summary'     => $request->summary,
            'status'      => ($request->status == true) ? Post::STATUS['ACTIVE'] : Post::STATUS['INACTIVE'],
            'avatar_post' => $request->avatar_post,
            'uri_post'    => $request->uri_post,
            'category_id' => $request->category_id,
            'user_id'     => $user->id,
        ]);

        $this->checkAndGenerateTag($request->tag, $post->id);

        return $this->responses(trans('notication.create.success'), Response::HTTP_OK, $post);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $user = $request->user();
        $post = $this->post->find($id);

        if(empty($post)) {
            return $this->responseErrors('post', trans('validation.not_found', ['attribute' => 'Bài viết']));
        }

        //check url exists
        if($this->url->findByUri($request->uri_post) && $request->uri_post != $post->uri_post) {
            return $this->responseErrors('uri_post', trans('validation.unique', ['attribute' => 'liên kết bài viết']));
        }

        if(!empty($request->avatar_post)) {
            $post->avatar_post  = $request->avatar_post;
        }

        $request->tag = json_decode($request->tag);
        if(count($request->tag) > 10) {
            return $this->responseErrors('tag', trans('validation.max.numeric', ['attribute' => 'tag', 'max' => 10]));
        }

        $url = $this->url->findByUri($post->uri_post);
        $url->url_title     = $request->title;
        $url->uri           = $request->uri_post;
        $url->save();
        $post->content      = $request->content;
        $post->title        = $request->title;
        $post->summary      = $request->summary;
        $post->status       = $request->status;
        $post->uri_post     = $request->uri_post;
        $post->category_id  = $request->category_id;
        $post->user_id      = $user->id;

        $post->save();

        $post->tags()->detach();
        $this->checkAndGenerateTag($request->tag, $post->id);

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    public function edit($id)
    {
        $post = $this->post->with('tags')->find($id);

        if(empty($post)) {
            return response()->json([
                'message'     => 'Incorect route',
                'status'      => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($post);
    }

    public function checkAndGenerateTag($tag, $post_id) {
        $tags = $this->tag->all();

        //check tag exist
        foreach($tag as $item) {
            if(!$tags->contains('tag', $item)) {
                $this->tag->create([
                    'tag'     => $item,
                    'uri_tag' => Uuid::generate(4)->string
                ]);
            }

            $this->tag->findByTag($item)
            ->posts()
            ->attach($post_id);
        }
    }

    public function destroy($id)
    {
        $post = $this->post->find($id);
        if (empty($post)) {
            return $this->responseErrors('post', trans('validation.not_found', ['attribute' => 'Bài viết']));
        }

        $this->url->findByUri($post->uri_post)->delete();

        Storage::delete($post->avatar_post);
        $post->delete();

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }

}
