<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
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

        $this->userRepository->skipPresenter();
        $this->postRepository->skipPresenter();
    }

    public function index(Request $request)
    {
        $dateStart = Carbon::now()->subMonth(4)->startOfMonth();
        $dateEnd   = Carbon::now()->subMonth(1)->endOfMonth();
        $data      = $this->postRepository->statisticWithPostMonth($dateStart, $dateEnd);
        $statistic = [];

        foreach($data as $item) {
            $statistic['dataLabel'][] = "$item->month-$item->year";
            $statistic['data_view'][] = $item->count_views;
            $statistic['data_post'][] = $item->count_posts;
        }

        $dashboard = [
            'data' => [
                'type'      => 'statistic',
                'postMonth' => $this->postRepository->countPostInMonthCurrent(),
                'postAll'   => $this->postRepository->countPostWithRole(),
                'statistic' => $statistic,
            ]
        ];

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, $dashboard);
    }
}
