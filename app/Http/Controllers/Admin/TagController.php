<?php

namespace App\Http\Controllers\Admin;

use Entrust;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;

use Uuid;
use App\Entities\Role;
use App\Http\Requests\Admin\Tag\TagRequest;
use App\Repositories\Contracts\TagRepository;

class TagController extends BaseController
{
    /**
     * $tagRepository
     * @var repository
     */
    protected $tagRepository;

    /**
     * giới hạn danh sách của tags.
     * @var integer
     */
    protected $paginate = 10;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->middleware('admin')->except(['index']);
    }

    /**
     * Hiển thị tất cả tags
     * @param  Request $request
     * @return object
     */
    public function index(Request $request)
    {
        $tags = $this->tagRepository
            ->paginate($this->paginate);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('tags'));
    }

    /**
     * cập nhật tag và url theo uri_tag
     *
     * @param  TagRequest $request đây là những nguyên tắc ràng buộc khi request được chấp nhận
     * @param  int                $id      là id của danh muc
     * @return object
     */
    public function update(TagRequest $request, $id)
    {
        $tag = $this->tagRepository->skipPresenter()->find($id);
        if($request->tag == $tag->tag) {
            return $this->responseErrors('tag', 'Bạn không thay đổi gì.');
        }

        if($request->tag != $tag->tag && $this->tagRepository->all()->contains('tag', $request->tag)) {
            return $this->responseErrors('tag', trans('validation.unique', ['attribute' => 'tag']));
        }

        $credentials            = $request->all();
        $credentials['uri_tag'] = Uuid::generate(4)->string;
        $this->tagRepository->update($credentials, $id);

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    /**
     * xóa danh mục theo id
     * @param int $id đây là id tìm kiếm của danh mục
     * @return object
     */
    public function destroy($id)
    {
        $this->tagRepository->delete($id);

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }
}
