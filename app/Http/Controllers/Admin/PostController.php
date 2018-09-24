<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

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
    protected $paginate = 10;

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
    }

    public function index(Request $request)
    {
        $posts = $this->post
        ->latest()
        ->paginate(10);

        return response()->json($paginate);
    }

    public function show($id)
    {
        $post = $this->post->find($id);

        return response()->json($post);
    }

    public function store(CreatePostRequest $request)
    {
        $user = $request->user();

        if(count($request->tag) > 10) {
            return $this->responseErrors('tag', 'The tag may not be greater than 10.');
        }

        $this->url->create([
            'url_title'     => $request->title,
            'uri'           => $request->uri_post,
        ]);

        $post = $this->post->create([
            'content'       => $request->content,
            'title'         => $request->title,
            'status'        => $request->status,
            'avatar_post'   => $request->avatar_post,
            'uri_post'      => $request->uri_post,
            'category_id'   => $request->category_id,
            'user_id'       => $user->id,
        ]);

        $this->checkAndGenerateTag($request->tag, $post->id);

        return $this->responses(trans('notication.create.success'), Response::HTTP_OK, $post);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $user = $request->user();
        $post = $this->post->find($id);

        //check url exists
        if($this->url->findByUri($request->uri_post) && $request->uri_post != $post->uri_post) {
            return $this->responseErrors('uri_post', 'The uri post has already been taken.');
        }

        if(count($request->tag) > 10) {
            return $this->responseErrors('tag', 'The tag may not be greater than 10.');
        }

        $url = $this->url->findByUri($post->uri_post);
        $url->url_title     = $request->title;
        $url->uri           = $request->uri_post;
        $url->save();

        $post->content      = $request->content;
        $post->title        = $request->title;
        $post->status       = $request->status;
        // $post->avatar_post  = $request->avatar_post;
        $post->uri_post     = $request->uri_post;
        $post->category_id  = $request->category_id;
        $post->user_id      = $user->id;

        $post->save();

        $post->tags()->detach();
        $this->checkAndGenerateTag($request->tag, $post->id);

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    public function checkAndGenerateTag($tag, $post_id) {
        $tags = $this->tag->all();

        //check tag exist
        foreach($tag as $item) {
            if(!$tags->contains('tag', $item)) {
                $this->tag->create(['tag' => $item]);
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
            throw \Illuminate\Validation\ValidationException::withMessages(['loi']);
        }

        $this->url->findByUri($post->uri_post)->delete();

        $post->delete();

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }

}
