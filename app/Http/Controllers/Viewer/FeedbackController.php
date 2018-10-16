<?php

namespace App\Http\Controllers\Viewer;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\BaseController;

use App\Http\Requests\FeedbackRequest;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\FeedbackRepository;

class FeedbackController extends BaseController
{
    protected $userRepository;
    protected $feedbackRepository;

    public function __construct(
        FeedbackRepository $feedbackRepository,
        UserRepository $userRepository
    ){
        $this->feedbackRepository = $feedbackRepository;
        $this->userRepository     = $userRepository;
    }

    public function create(FeedbackRequest $request)
    {
        $this->feedbackRepository->create($request->all());

        return $this->responses(trans('notication.create.success'), Response::HTTP_OK);
    }
}
