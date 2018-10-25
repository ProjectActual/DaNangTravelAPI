<?php

namespace App\Http\Controllers\Admin;

use DB;
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
use App\Http\Requests\Admin\Post\CheckHotRequest;
use App\Http\Requests\Admin\Post\FilterDataRequest;
use App\Http\Requests\Admin\Post\CreatePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;
use App\Http\Requests\Admin\Post\CheckSliderRequest;

class PostController extends BaseController
{
    /**
     *the number of elements in a page
     * @var int
    */
    protected $paginate = 5;

    /** @var instance */
    protected $tagRepository;
    protected $urlRepository;
    protected $postRepository;

    public function __construct(
        TagRepository $tagRepository,
        UrlRepository $urlRepository,
        PostRepository $postRepository
    ){
        $this->tagRepository      = $tagRepository;
        $this->urlRepository      = $urlRepository;
        $this->postRepository     = $postRepository;

        $this->postRepository->pushCriteria(new FilterPostWithCTVCriteria());
        $this->middleware('admin')->only(['showSlider', 'showHot']);
    }

    /**
     * show all posts
     *
     * @return Illuminate\Http\Response
     */
    public function index(FilterDataRequest $request)
    {
        if(empty($request->sort)) {
            $posts = $this->postRepository
                ->orderBy('is_slider', 'desc')
                ->orderBy('is_hot', 'desc');
        }else {
            //convert string to array and get value to sort
            $sort = explode("-", $request->sort);
            $posts = $this->postRepository
                ->order($sort[0], $sort[1]);
        }
        //search Category
        if(empty($request->search_category)) {
            $posts = $posts->paginate($this->paginate);
        }else {
            $posts = $posts->filterByCategory($request->search_category)
                ->paginate($this->paginate);
        }
        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('posts'));
    }

    /**
     * show post detail
     *
     * @param  integer  $id     this is the key to find post detail
     * @return Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $post = $this->postRepository->find($id);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('post'));
    }

    /**
     * create new post by user
     *
     * @param  CreatePostRequest $request These are binding rules when requests are accepted
     * @return Illuminate\Http\Response
     */
    public function store(CreatePostRequest $request)
    {
        $request->tag     = json_decode($request->tag);
        //the number of tags in a post only 10 values
        if (count($request->tag) > 10) {
            return $this->responseErrors('tag', trans('validation.max.numeric', ['attribute' => 'tag', 'max' => 10]));
        }
        DB::beginTransaction();
        try {
            $urlCredentials  = [
                'url_title'     => $request->title,
                'uri'           => $request->uri_post,
            ];
            //create url and post with uri_post
            $this->urlRepository->create($urlCredentials);
            $postCredentials            = $request->except('tag');
            $postCredentials['user_id'] = $request->user()->id;
            $post                       = $this->postRepository->skipPresenter()->create($postCredentials);
            //check tag if not exists, save it
            $this->checkAndGenerateTag($request->tag, $post->id);
            DB::commit();
            return $this->responses(trans('notication.create.success'), Response::HTTP_OK);
        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * update information post
     *
     * @param  UpdatePostRequest $request These are binding rules when requests are accepted
     * @param  int $id this is the key to find and update a post
     * @return Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, $id)
    {
        //parse string to array to check
        $request->tag = json_decode($request->tag);
        if(count($request->tag) > 10) {
            return $this->responseErrors('tag', trans('validation.max.numeric', ['attribute' => 'tag', 'max' => 10]));
        }
        DB::beginTransaction();
        try {
            $post            = $this->postRepository->skipPresenter()->find($id);
            $postCredentials = $request->except('tag', 'avatar_post');
            // check if the $request->avatar_post is not empty update avatar, the reverse is do nothing
            if(!empty($request->avatar_post)) {
                $postCredentials['avatar_post'] = $request->avatar_post;
            }
            //check role is CTV , status is inactive
            if(!Entrust::hasRole(Role::NAME[1])) {
                $postCredentials['status'] = Post::STATUS['INACTIVE'];
            }
            //check url exists
            if($this->urlRepository->findByUri($request->uri_post) && $request->uri_post != $post->uri_post) {
                return $this->responseErrors('uri_post', trans('validation.unique', ['attribute' => 'liên kết bài viết']));
            }
            $url = $this->urlRepository->findByUri($post->uri_post);
            $urlCredentials  = [
                'url_title' => $request->title,
                'uri'       => $request->uri_post,
            ];
            $this->urlRepository->update($urlCredentials, $url->id);
            //update post and detach all tags in post
            $post = $this->postRepository
                ->updateAndDetachTag($postCredentials, $post->id);
            $this->checkAndGenerateTag($request->tag, $post->id);
            DB::commit();
            return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check to initialize the tag for the article, if the tag is not available,
     * then create a tag, if so, then attach to the article
     *
     * @param  array $tag array from request
     * @param  int $post_id this is the key to find a post
     * @return void
     */
    public function checkAndGenerateTag($tag, $post_id)
    {
        $tags = $this->tagRepository->skipPresenter()->all();

        //check tag exist
        foreach($tag as $item) {
            if(!$tags->contains('tag', $item)) {
                $this->tagRepository->create([
                    'tag'     => $item,
                    'uri_tag' => Uuid::generate(4)->string
                ]);
            }

            $this->tagRepository->findByTag($item)
            ->posts()
            ->attach($post_id);
        }
    }

    /**
     * delete information post
     *
     * @param  int  $id this is the key to find a post
     * @return Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->postRepository->skipPresenter();
            $post = $this->postRepository->find($id);
            $this->urlRepository->findByUri($post->uri_post)->delete();
            $this->postRepository->delete($id);
            DB::commit();
            return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * update the post is show in slider
     *
     * @param  CheckSliderRequest $request These are binding rules when requests are accepted
     * @param  int $id this is the key to find the post
     * @return Illuminate\Http\Response
     */
    public function showSlider(CheckSliderRequest $request, $id)
    {
        $this->validatorSliderAndHot($id);
        // The slider test only shows 3 elements
        if( $this->postRepository->findByIsSlider()->count() >= 3 && $request->is_slider == Post::IS_SLIDER['NO']) {
            $this->responseErrors('post', trans('validation_custom.posts.limit', ['attribute' => 'Slider và tin nổi bật', 'max' => 3]));
        }
        //Check if $ request-> is_slider is YES then switch back to NO and vice versa
        $credentials = [
            'is_slider' => ($request->is_slider == Post::IS_SLIDER['YES']) ? Post::IS_SLIDER['NO'] : Post::IS_SLIDER['YES']
        ];
        $this->postRepository->update($credentials, $id);
        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    /**
     * update the post is show in hot
     *
     * @param  CheckSliderRequest $request These are binding rules when requests are accepted
     * @param  int $id this is the key to find the post
     * @return Illuminate\Http\Response
     */
    public function showHot(CheckHotRequest $request, $id)
    {
        $this->validatorSliderAndHot($id);
        //The slider test only shows 3 elements
        if( $this->postRepository->findByIsHot()->count() >= 3 && $request->is_hot == Post::IS_HOT['NO']) {
            $this->responseErrors('post', trans('validation_custom.posts.limit', ['attribute' => 'Slider và tin nổi bật', 'max' => 3]));
        }
        //Check if $ request-> is_slider is YES then switch back to NO and vice versa
        $credentials = [
            'is_hot' => ($request->is_hot == Post::IS_HOT['YES']) ? Post::IS_HOT['NO'] : Post::IS_HOT['YES']
        ];
        $this->postRepository->update($credentials, $id);
        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    /**
     * catch the constraints when updating the slider and hot
     *
     * @param  int id this is the key to find the post
     * @return void
     */
    public function validatorSliderAndHot($id)
    {
        $post = $this->postRepository->skipPresenter()->find($id);
        //Check if the post is not active, not selected as slider or hot
        if( $post->status === Post::STATUS['INACTIVE'] ) {
            $this->responseErrors('post', trans('validation_custom.posts.selected'));
        }
    }
}
