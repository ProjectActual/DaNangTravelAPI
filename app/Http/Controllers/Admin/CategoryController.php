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
     * Hiển thị tất cả danh mục
     * @param  Request $request
     * @return object
     */
    public function index(Request $request)
    {
        $categories = $this->categoryRepository->all();

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('categories'));
    }


    /**
     *  Tạo danh mục, chi áp dụng với admin
     *
     * @param  CreateCategoryRequest $request đây là những nguyên tắc ràng buộc khi request được chấp nhận
     *
     * @return object
     */
    public function store(CreateCategoryRequest $request)
    {
        $this->categoryRepository->skipPresenter();

        if($this->categoryRepository->all()->count() >= 10) {
            return $this->responseErrors('category', trans('validation.max.numeric', ['attribute' => 'danh mục', 'max' => 10]));
        }

        $urlCredentials = [
            'url_title'   => $request->name_category,
            'uri'         => $request->uri_category,
        ];

        $url = $this->urlRepository->create($urlCredentials);

        $category = $this->categoryRepository->create($request->all());

        return $this->responses(trans('notication.create.success'), Response::HTTP_OK);
    }

    /**
     * cập nhật danh mục và url theo uri_category
     *
     * @param  UpdateCategoryRequest $request đây là những nguyên tắc ràng buộc khi request được chấp nhận
     * @param  int                $id      là id của danh muc
     * @return object
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryRepository->skipPresenter()->find($id);

        if($this->urlRepository->findByUri($request->uri_category) && $request->uri_category != $category->uri_category) {
            return $this->responseErrors('uri_category', trans('validation.unique', ['attribute' => 'liên kết danh mục']));
        }

        // kiểm tra là loại danh mục là duy nhất
        if($this->categoryRepository->findByField('type_category', $request->type_category)->isNotEmpty() && $request->type_category != $category->type_category) {
            return $this->responseErrors('type_category', trans('validation.unique', ['attribute' => 'loại danh mục']));
        }

        $url = $this->urlRepository->findByUri($category->uri_category);
        $urlCredentials = [
            'url_title'   => $request->name_category,
            'uri'         => $request->uri_category,
        ];
        $this->urlRepository->update($urlCredentials, $url->id);

        $this->categoryRepository->update($request->all(), $category->id);

        return $this->responses(trans('notication.edit.success'), Response::HTTP_OK);
    }

    /**
     * thông tin chi tiết của danh mục
     * @param int $id đây là id tìm kiếm của danh mục
     * @return object
     */
    public function edit($id)
    {
        $category = $this->categoryRepository->find($id);

        if(empty($category)) {
            return $this->responseErrors('category', trans('validation.not_found', ['attribute' => 'Danh mục']));
        }

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('category'));
    }

    /**
     * xóa danh mục theo id
     * @param int $id đây là id tìm kiếm của danh mục
     * @return object
     */
    public function destroy($id)
    {
        $category = $this->categoryRepository->delete($id);

        $this->urlRepository->findByUri($category->uri_category)->delete();

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }
}
