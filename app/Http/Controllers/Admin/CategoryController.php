<?php

namespace App\Http\Controllers\Admin;

use Entrust;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Entities\Role;
use App\Http\Controllers\BaseController;
use App\Repositories\Contracts\UrlRepository;
use App\Repositories\Contracts\CategoryRepository;
use App\Http\Requests\Admin\Category\CreateCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;

class CategoryController extends BaseController
{
    protected $url;
    protected $category;

    public function __construct(
        UrlRepository $url,
        CategoryRepository $category
    ){
        $this->url       = $url;
        $this->category  = $category;
    }

    public function index(Request $request)
    {
        $categories = $this->category->all();

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('categories'));
    }


    /**
     * @param  CreateCategoryRequest $request
     *
     * @return json
     */
    public function store(CreateCategoryRequest $request)
    {
        $this->category->skipPresenter();

        if(!Entrust::hasRole(Role::NAME[1])) {
            return $this->responseException('You do not have access to the router', 401);
        }

        if($this->category->all()->count() >= 10) {
            return $this->responseErrors('category', trans('validation.max.numeric', ['attribute' => 'danh mục', 'max' => 10]));
        }

        $url = $this->url->create([
            'url_title'   => $request->name_category,
            'uri'         => $request->uri_category,
        ]);

        $category = $this->category->create([
            'name_category' => $request->name_category,
            'uri_category'  => $request->uri_category,
            'type_category' => $request->type_category,
            'description'   => $request->description,
        ]);

        return $this->responses(trans('notication.create.success'), Response::HTTP_OK);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        if(!Entrust::hasRole(Role::NAME[1])) {
            return $this->responseException('You do not have access to the router', 401);
        }

        $this->category->skipPresenter();
        $category = $this->category->find($id);

        if($this->url->findByUri($request->uri_category) && $request->uri_category != $category->uri_category) {
            return $this->responseErrors('uri_category', trans('validation.unique', ['attribute' => 'liên kết danh mục']));
        }

        if($this->category->findByField('type_category', $request->type_category)->isNotEmpty() && $request->type_category != $category->type_category) {
            return $this->responseErrors('type_category', trans('validation.unique', ['attribute' => 'loại danh mục']));
        }

        $url = $this->url->findByUri($category->uri_category);
        $url->url_title     = $request->name_category;
        $url->uri           = $request->uri_category;
        $url->save();

        $category->name_category     = $request->name_category;
        $category->uri_category      = $request->uri_category;
        $category->type_category     = $request->type_category;
        $category->description       = $request->description;
        $category->save();

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    public function edit($id)
    {
        if(!Entrust::hasRole(Role::NAME[1])) {
            return $this->responseException('You do not have access to the router', 401);
        }

        $category = $this->category->find($id);

        if(empty($category)) {
            return $this->responseErrors('category', trans('validation.not_found', ['attribute' => 'Danh mục']));
        }

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('category'));
    }

    public function destroy($id)
    {
        if(!Entrust::hasRole(Role::NAME[1])) {
            return $this->responseException('You do not have access to the router', 401);
        }

        $this->category->skipPresenter();
        $category = $this->category->find($id);
        if (empty($category)) {
            return $this->responseErrors('category', trans('validation.not_found', ['attribute' => 'Danh mục']));
        }

        $this->url->findByUri($category->uri_category)->delete();

        $category->delete();

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }
}
