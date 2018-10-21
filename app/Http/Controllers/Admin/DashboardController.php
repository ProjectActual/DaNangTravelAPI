<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\BaseController;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\PostRepository;

class DashboardController extends BaseController
{
    protected $userRepository;
    protected $postRepository;

    public function __construct(
        UserRepository $userRepository,
        PostRepository $postRepository
    ){
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;

        $this->postRepository->skipPresenter();
    }

    public function index(Request $request)
    {
        $dashboard = [
            'postMonth' => $this->postRepository->countPostInMonthCurrent(),
            'postAll'  => $this->postRepository->countPostWithRole(),
        ];

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, $dashboard);
    }
}
