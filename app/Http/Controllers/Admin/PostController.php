<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use Uuid;
use Entrust;
use App\Entities\Post;
use App\Entities\Role;
use App\Services\SendMail;
use App\Http\Controllers\BaseController;
use App\Notifications\PasswordResetRequest;
use App\Repositories\Contracts\UrlRepository;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\PostRepository;
use App\Criteria\Post\FilterPostWithCTVCriteria;
use  App\Http\Requests\Admin\Post\CheckHotRequest;
use App\Repositories\Contracts\CategoryRepository;
use App\Http\Requests\Admin\Post\CreatePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;
use App\Http\Requests\Admin\Post\CheckSliderRequest;

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
        $this->tag      = $tag;
        $this->url      = $url;
        $this->post     = $post;
        $this->category = $category;

        $this->tag->skipPresenter();
    }

    public function index(Request $request)
    {
        $this->post->pushCriteria(new FilterPostWithCTVCriteria());
        $posts = $this->post->latest()->paginate($this->paginate);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('posts'));
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $post = $this->post->skipPresenter()->find($id);

        if(!Entrust::hasRole(Role::NAME[1]) && $post['user_id'] != $user->id) {
            return $this->responseException('You do not have access to the router', 401);
        }

        $post = $post->presenter();

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('post'));
    }

    public function store(CreatePostRequest $request)
    {
        $this->post->skipPresenter();
        $user         = $request->user();
        $request->tag = json_decode($request->tag);

        if(count($request->tag) > 10) {
            return $this->responseErrors('tag', trans('validation.max.numeric', ['attribute' => 'tag', 'max' => 10]));
        }

        $this->url->create([
            'url_title'     => $request->title,
            'uri'           => $request->uri_post,
        ]);

        if(!Entrust::hasRole(Role::NAME[1])) {
            $request->status = Post::STATUS['INACTIVE'];
        }

        $post   = $this->post->create([
            'content'     => $request->content,
            'title'       => $request->title,
            'summary'     => $request->summary,
            'status'      => $request->status,
            'avatar_post' => $request->avatar_post,
            'uri_post'    => $request->uri_post,
            'category_id' => $request->category_id,
            'user_id'     => $user->id,
        ]);

        $this->checkAndGenerateTag($request->tag, $post->id);

        return $this->responses(trans('notication.create.success'), Response::HTTP_OK);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $this->post->skipPresenter();
        $user = $request->user();
        $post = $this->post->find($id);
        if(!Entrust::hasRole(Role::NAME[1]) && $post->user_id != $user->id) {
            return $this->responseException('You do not have access to the router', Response::HTTP_UNAUTHORIZED);
        }

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

    public function destroy(Request $request, $id)
    {
        $this->post->skipPresenter();
        $post = $this->post->find($id);
        $user = $request->user();

        if(!Entrust::hasRole(Role::NAME[1]) && $post->user_id != $user->id) {
            return $this->responseException('You do not have access to the router', Response::HTTP_UNAUTHORIZED);
        }

        if (empty($post)) {
            return $this->responseException('Incorect route', Response::HTTP_NOT_FOUND);
        }

        $this->url->findByUri($post->uri_post)->delete();

        Storage::delete($post->avatar_post);
        $post->delete();

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }

    public function showSlider(CheckSliderRequest $request, $id)
    {
        $this->post->skipPresenter();

        $post = $this->post->find($id);
        $user = $request->user();

        if(!Entrust::hasRole(Role::NAME[1]) && $post->user_id != $user->id) {
            return $this->responseException('You do not have access to the router', Response::HTTP_UNAUTHORIZED);
        }

        if( $this->post->findByIsSlider()->count() >= 3 && $request->is_slider == Post::IS_SLIDER['NO']) {
            $this->responseErrors('post', trans('validation_custom.posts.limit', ['attribute' => 'Slider và tin nổi bật', 'max' => 3]));
        }

        if( $post->status === Post::STATUS['INACTIVE'] ) {
            $this->responseErrors('post', trans('validation_custom.posts.selected'));
        }

        if (empty($post)) {
            return $this->responseException('Incorect route', Response::HTTP_NOT_FOUND);
        }

        $post->is_slider = ($request->is_slider == Post::IS_SLIDER['YES']) ? Post::IS_SLIDER['NO'] : Post::IS_SLIDER['YES'];
        $post->save();

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    public function showHot(CheckHotRequest $request, $id)
    {
        $this->post->skipPresenter();

        $post = $this->post->find($id);
        $user = $request->user();

        if(!Entrust::hasRole(Role::NAME[1]) && $post->user_id != $user->id) {
            return $this->responseException('You do not have access to the router', Response::HTTP_UNAUTHORIZED);
        }

        if( $this->post->findByIsHot()->count() >= 3 && $request->is_hot == Post::IS_HOT['NO']) {
            $this->responseErrors('post', trans('validation_custom.posts.limit', ['attribute' => 'Slider và tin nổi bật', 'max' => 3]));
        }

        if( $post->status === Post::STATUS['INACTIVE'] ) {
            $this->responseErrors('post', trans('validation_custom.posts.selected'));
        }

        if (empty($post)) {
            return $this->responseException('Incorect route', Response::HTTP_NOT_FOUND);
        }

        $post->is_hot = ($request->is_hot == Post::IS_HOT['YES']) ? Post::IS_HOT['NO'] : Post::IS_HOT['YES'];
        $post->save();

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }
}
