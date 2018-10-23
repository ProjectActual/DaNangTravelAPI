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
     * the number of elements in a page.
     * @var int
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->middleware('admin')->except(['index']);
    }

    /**
     * show all tags
     * @param  Request $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = $this->tagRepository
            ->all();
        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('tags'));
    }

    /**
     * update information tag
     *
     * @param  TagRequest $request These are binding rules when requests are accepted
     * @param  int $id this is the key to find and update tags
     * @return Illuminate\Http\Response
     */
    public function update(TagRequest $request, $id)
    {
        $tag = $this->tagRepository->skipPresenter()->find($id);
        //Check the value has been changed
        if($request->tag == $tag->tag) {
            return $this->responseErrors('tag', trans('validation_custom.change'));
        }
        //Check and indicate that the tag is unique
        if($request->tag != $tag->tag && $this->tagRepository->all()->contains('tag', $request->tag)) {
            return $this->responseErrors('tag', trans('validation.unique', ['attribute' => 'tag']));
        }
        $credentials            = $request->all();
        $credentials['uri_tag'] = Uuid::generate(4)->string;
        $this->tagRepository->update($credentials, $id);
        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    /**
     * delete tag
     * @param int $id this is the key to find and destroy tags
     * @return Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->tagRepository->delete($id);
        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }
}
