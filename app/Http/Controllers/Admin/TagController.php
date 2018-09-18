<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;

use App\Repositories\Contracts\TagRepository;

class TagController extends BaseController
{
    protected $tag;

    public function __construct(TagRepository $tag)
    {
        $this->tag = $tag;
    }

    public function index(Request $request)
    {
        $tags = $this->tag->withCount('posts')->get();

        return response()->json($tags);
    }

    public function update(Request $request, $id)
    {
        $tag = $this->tag->find($id);

        if($request->tag != $tag->tag && $this->tag->all()->contains('tag', $request->tag)) {
            return $this->responseErrors('tag', 'The tag has already been taken.');
        }

        $tag->tag = $request->tag;
        $tag->save();

        return $this->responses(trans('notication.edit.success'), 200);
    }

    public function destroy($id)
    {
        $this->tag->find($id)->delete();

        return $this->responses(trans('notication.delete.success'), 200);
    }
}