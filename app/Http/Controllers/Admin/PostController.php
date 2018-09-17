<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Repositories\Contracts\UrlRepository;
use App\Repositories\Contracts\PostRepository;
use App\Http\Requests\Admin\Post\CreatePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;

class PostController extends Controller
{
    protected $url;
    protected $post;

    public function __construct(
        UrlRepository $url,
        PostRepository $post
    ){
        $this->url         = $url;
        $this->post        = $post;
    }

    public function index(Request $request)
    {
        $posts = $this->post
        ->latest()
        ->get();

        return response()->json($posts);
    }

    public function show($id)
    {
        $post = $this->post->find($id);

        return response()->json($post);
    }

    public function store(CreatePostRequest $request)
    {
        $user = $request->user();

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

        return responses('Create article successfully', Response::HTTP_OK, $post);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $user = $request->user();
        $post = $this->post->find($id);

        if($this->url->findWithUri($request->uri_post) && $request->uri_post != $post->uri_post) {
            throw \Illuminate\Validation\ValidationException::withMessages(['loi']);
        }

        $url = $this->url->findWithUri($post->uri_post);
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

        return responses('Update article successfully', Response::HTTP_OK, $post);
    }

    public function destroy($id)
    {
        $post = $this->post->find($id);
        if (empty($post)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['loi']);
        }

        $this->url->findWithUri($post->uri_post)->delete();

        $post->delete();

        return responses('Delete article successfully', Response::HTTP_OK);
    }

}
