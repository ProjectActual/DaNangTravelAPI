<?php

namespace App\Http\Controllers\Admin;

use DB;
use Entrust;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Entities\Category;
use App\Entities\Role;
use App\Http\Controllers\BaseController;
use App\Repositories\Contracts\UrlRepository;
use App\Repositories\Contracts\CategoryRepository;
use App\Http\Requests\Category\CategoryStatusRequest;
use App\Http\Requests\Admin\Category\CreateCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;

class CategoryController extends BaseController
{
    /**
     * $url, $category
     * @var repository
     */
    protected $urlRepository;
    protected $categoryRepository;

    public function __construct(
        UrlRepository $urlRepository,
        CategoryRepository $categoryRepository
    ){
        $this->urlRepository       = $urlRepository;
        $this->categoryRepository  = $categoryRepository;

        $this->middleware('admin')->except(['index']);
    }

    /**
     * Show all categories
     * @param  Request $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = $this->categoryRepository->withCount('posts')->all();
        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, $categories);
    }

    /**
     *  create category, only admin
     *
     * @param  CreateCategoryRequest $request These are binding rules when requests are accepted
     * @return Illuminate\Http\Response
     */
    public function store(CreateCategoryRequest $request)
    {
        $this->categoryRepository->skipPresenter();
        //Only up to 10 categories can be created
        if($this->categoryRepository->all()->count() >= 10) {
            return $this->responseErrors('category', trans('validation.max.numeric', ['attribute' => 'danh mục', 'max' => 10]));
        }
        DB::beginTransaction();
        try {
            //Create url with url_category
            $urlCredentials = [
                'url_title'   => $request->name_category,
                'uri'         => $request->uri_category,
            ];
            $url = $this->urlRepository->create($urlCredentials);
            //Create category
            $category = $this->categoryRepository->create($request->all());
            DB::commit();
            return $this->responses(trans('notication.create.success'), Response::HTTP_OK);
        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * update category and url with uri_category
     *
     * @param  UpdateCategoryRequest $request These are binding rules when requests are accepted
     * @param  int                $id find and update category
     * @return Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryRepository->skipPresenter()->find($id);
        //check uri_category is unique
        if($this->urlRepository->findByUri($request->uri_category) && $request->uri_category != $category->uri_category) {
            return $this->responseErrors('uri_category', trans('validation.unique', ['attribute' => 'liên kết danh mục']));
        }
        // check category is unique
        if($this->categoryRepository->findByField('type_category', $request->type_category)->isNotEmpty() && $request->type_category != $category->type_category) {
            return $this->responseErrors('type_category', trans('validation.unique', ['attribute' => 'loại danh mục']));
        }
        //update url
        DB::beginTransaction();
        try {
            $url = $this->urlRepository->findByUri($category->uri_category);
            $urlCredentials = [
                'url_title'   => $request->name_category,
                'uri'         => $request->uri_category,
            ];
            $this->urlRepository->update($urlCredentials, $url->id);
            //update category
            $this->categoryRepository->update($request->all(), $category->id);
            DB::commit();
            return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * get infortion category
     * @param int $id find and update category
     * @return Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = $this->categoryRepository->withCount('posts')->find($id);
        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, $category);
    }

    /**
     * delete category with id
     * @param int $id find and delete category
     * @return Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->checkStatus($id);
        //delete category and url vie uri_category
        DB::beginTransaction();
        try {
            $category = $this->categoryRepository->skipPresenter()->find($id);
            $this->categoryRepository->delete($category->id);
            $this->urlRepository->findByUri($category->uri_category)->delete();
            DB::commit();
            return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approval(CategoryStatusRequest $request, $id)
    {
        $this->checkStatus($id);
        $this->categoryRepository->skipPresenter();
        if($this->categoryRepository->findByActive()->count() >= 6 && $request->status == Category::STATUS[1]) {
            $this->responseErrors('categories', 'Chỉ được active tối đa 6 danh mục');
        }
        $credential = [
            'status' => $request->status,
        ];
        $category = $this->categoryRepository->update($credential, $id);
        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    public function checkStatus($key)
    {
        $prohibitedList = [
           2 => Category::CODE_CATEGORY['DIEM_DEN'],
           3 => Category::CODE_CATEGORY['SU_KIEN'],
           4 => Category::CODE_CATEGORY['AM_THUC'],
        ];
        if(array_key_exists($key, $prohibitedList)) {
            return $this->responseErrors('Prohibited list', 'Điểm đến, Sự kiện, Ẩm thực là 3 danh sách mặc định, vì vậy bạn không thể hủy, xóa nó');
        }
    }
}
