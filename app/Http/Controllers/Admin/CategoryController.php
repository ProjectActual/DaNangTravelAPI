<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Repositories\Contracts\UrlRepository;
use App\Repositories\Contracts\CategoryRepository;
use App\Http\Requests\Admin\Category\CreateCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    protected $url;
    protected $category;

    public function __construct(
        UrlRepository $url,
        CategoryRepository $category
    ){
        $this->url  = $url;
        $this->category  = $category;
    }

    public function index(Request $request)
    {
        $category = $this->category->all();

        return response()->json($category);
    }

    public function store(CreateCategoryRequest $request)
    {
        $url = $this->url->create([
            'url_title'   => $request->name_category,
            'uri'         => $request->uri_category,
        ]);

        $category = $this->category->create([
            'name_category'     => $request->name_category,
            'uri_category'      => $request->uri_category,
            'description'       => $request->description
        ]);

        return responses(trans('notication.create.success'), Response::HTTP_OK);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->category->find($id);

        if($this->url->findByUri($request->uri_category) && $request->uri_category != $category->uri_category) {
            throw \Illuminate\Validation\ValidationException::withMessages(['loi']);
        }

        $url = $this->url->findByUri($category->uri_category);
        $url->url_title     = $request->name_category;
        $url->uri           = $request->uri_category;
        $url->save();

        $category->name_category     = $request->name_category;
        $category->uri_category      = $request->uri_category;
        $category->description       = $request->description;
        $category->save();

        return responses(trans('notication.edit.success'), Response::HTTP_OK);
    }
}
