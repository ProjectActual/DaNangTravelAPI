<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\BaseController;

use App\Repositories\Contracts\UserRepository;
use App\Criteria\CTV\StatictisWithPostCriteria;
use App\Http\Requests\Admin\User\StatictisRequest;

class StatisticController extends BaseController
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Thống kê theo tháng của cộng tác viên đăng bài, được chọn tối đa là 3 tháng sắp xếp theo tháng cao đến thấp
     * @param  Request $request truyền lên từ tháng năm, đến tháng năm
     * @return Illuminate\Http\Response
     */
    public function statistic(StatictisRequest $request)
    {
        $date = Carbon::parse($request->date)->startOfMonth();
        $this->userRepository->pushCriteria(new StatictisWithPostCriteria($date));
        $userStatistics = $this->userRepository->withCount(['posts'])->get();

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, $userStatistics);
    }
}
