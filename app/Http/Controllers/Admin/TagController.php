<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;

use App\Http\Requests\Admin\Tag\TagRequest;
use App\Repositories\Contracts\TagRepository;

class TagController extends BaseController
{
    protected $tag;
    protected $paginate = 15;

    public function __construct(TagRepository $tag)
    {
        $this->tag = $tag;
    }

    public function index(Request $request)
    {
        $tags = $this->tag
            ->searchWithTag($request->search)
            ->withCount('posts')
            ->paginate($this->paginate);

        return response()->json($tags);
    }

    public function update(TagRequest $request, $id)
    {
        $this->tag->skipPresenter();
        $tag = $this->tag->find($id);
        if($request->tag == $tag->tag) {
            return $this->responseErrors('tag', 'Bạn không thay đổi gì.');
        }

        if($request->tag != $tag->tag && $this->tag->all()->contains('tag', $request->tag)) {
            return $this->responseErrors('tag', trans('validation.unique', ['attribute' => 'tag']));
        }

        $tag->tag = $request->tag;
        $tag->save();

        return $this->responses(trans('notication.edit.success'), 200);
    }

    public function destroy($id)
    {
        $this->tag->skipPresenter();
        $this->tag->find($id)->delete();

        return $this->responses(trans('notication.delete.success'), 200);
    }
}
