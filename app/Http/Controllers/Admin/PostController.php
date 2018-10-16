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
use App\Http\Requests\Admin\Post\CheckHotRequest;
use App\Http\Requests\Admin\Post\CreatePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;
use App\Http\Requests\Admin\Post\CheckSliderRequest;

class PostController extends BaseController
{
    /** @var int
    số lượng phần từ được xuất hiện trong page
    */
    protected $paginate = 15;

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
    }

    /**
     * hiển thị tất cả bài viết
     * @return object
     */
    public function index(Request $request)
    {
        $posts = $this->postRepository->latest()->paginate($this->paginate);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('posts'));
    }

    /**
     * hiển thị chi tiết bài viết
     * @param  integer  $id     là id của bài viết
     * @return object
     */
    public function show(Request $request, $id)
    {
        $post = $this->postRepository->find($id);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('post'));
    }

    /**
     * Tạo bài viết bởi người dùng
     * @param  CreatePostRequest $request  những nguyên tắc ràng buộc khi request được chấp nhận
     * @return object
     */
    public function store(CreatePostRequest $request)
    {
        $request->tag     = json_decode($request->tag);

        if(count($request->tag) > 10) {
            return $this->responseErrors('tag', trans('validation.max.numeric', ['attribute' => 'tag', 'max' => 10]));
        }

        $urlCredentials  = [
            'url_title'     => $request->title,
            'uri'           => $request->uri_post,
        ];
        $this->urlRepository->create($urlCredentials);

        $postCredentials = $request->except('tag');
        $postCredentials['user_id'] = $request->user()->id;

        $post   = $this->postRepository->skipPresenter()->create($postCredentials);

        $this->checkAndGenerateTag($request->tag, $post->id);

        return $this->responses(trans('notication.create.success'), Response::HTTP_OK);
    }

    /**
     * [cập nhật thông tin bài viếtư
     * @param  UpdatePostRequest $request      những nguyên tắc ràng buộc khi request được chấp nhận
     * @param  int $id      id của bài viết
     * @return object
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $request->tag = json_decode($request->tag);

        if(count($request->tag) > 10) {
            return $this->responseErrors('tag', trans('validation.max.numeric', ['attribute' => 'tag', 'max' => 10]));
        }

        $post = $this->postRepository->skipPresenter()->find($id);

        $postCredentials = $request->except('tag', 'avatar_post');

        if(!empty($request->avatar_post)) {
            $postCredentials['avatar_post'] = $request->avatar_post;
        }

        if(!Entrust::hasRole(Role::NAME[1])) {
            $postCredentials['status'] = Post::STATUS['INACTIVE'];
        }

        //check url exists
        if($this->urlRepository->findByUri($request->uri_post) && $request->uri_post != $post->uri_post) {
            return $this->responseErrors('uri_post', trans('validation.unique', ['attribute' => 'liên kết bài viết']));
        }

        $url = $this->urlRepository->findByUri($post->uri_post);
        $urlCredentials  = [
            'url_title'     => $request->title,
            'uri'           => $request->uri_post,
        ];

        $this->urlRepository->update($urlCredentials, $url->id);

        $post = $this->postRepository
            ->updateAndDetachTag($postCredentials, $post->id);

        $this->checkAndGenerateTag($request->tag, $post->id);

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    /**
     * kiểm tra để khởi tạo tag cho bài viết, nếu tag chưa có thì tạo tag, nếu đã có rồi thì
       attach vào bài viết
    .*
     * @param  Ẩy $tag     mảng tag được truyền lên từ request
     * @param  int $pót_id   id của bài viết để attach tag
     * @return [type]          [description]
     */
    public function checkAndGenerateTag($tag, $post_id) {
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
     * xóa thông tin bài viết
     * @param  int  $id      id của bài viết
     * @return object
     */
    public function destroy(Request $request, $id)
    {
        $this->postRepository->skipPresenter();
        $post = $this->postRepository->find($id);

        $this->urlRepository->findByUri($post->uri_post)->delete();

        $post->delete();

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }

    /**
     * cập nhật những bài viết được hiển thị ra slider
     * @param  CheckSliderRequest $request những nguyên tắc ràng buộc khi request được chấp nhận
     * @param  int             $id      id của bài viết
     * @return object
     */
    public function showSlider(CheckSliderRequest $request, $id)
    {
        $this->validatorSliderAndHot($id);

        if( $this->postRepository->findByIsSlider()->count() >= 3 && $request->is_slider == Post::IS_SLIDER['NO']) {
            $this->responseErrors('post', trans('validation_custom.posts.limit', ['attribute' => 'Slider và tin nổi bật', 'max' => 3]));
        }

        $credentials = [
            'is_slider' => ($request->is_slider == Post::IS_SLIDER['YES']) ? Post::IS_SLIDER['NO'] : Post::IS_SLIDER['YES']
        ];

        $this->postRepository->update($credentials, $id);

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    /**
     * cập nhật những bài viết được cho là hot
     * @param  CheckHotRequest $request những nguyên tắc ràng buộc khi request được chấp nhận
     * @param  int          $id      id của bài viết
     * @return object
     */
    public function showHot(CheckHotRequest $request, $id)
    {
        $this->validatorSliderAndHot($id);

        if( $this->postRepository->findByIsHot()->count() >= 3 && $request->is_hot == Post::IS_HOT['NO']) {
            $this->responseErrors('post', trans('validation_custom.posts.limit', ['attribute' => 'Slider và tin nổi bật', 'max' => 3]));
        }

        $credentials = [
            'is_hot' => ($request->is_hot == Post::IS_HOT['YES']) ? Post::IS_HOT['NO'] : Post::IS_HOT['YES']
        ];

        $this->postRepository->update($credentials, $id);

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    /**
     * bắt những nguyên tắc ràng buộc khi cập nhật slider và hot
     * @param  int $id      id của bài viết
     * @return void
     */
    public function validatorSliderAndHot($id)
    {
        $post = $this->postRepository->skipPresenter()->find($id);

        if( $post->status === Post::STATUS['INACTIVE'] ) {
            $this->responseErrors('post', trans('validation_custom.posts.selected'));
        }
    }
}
