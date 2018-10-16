<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;

use App\Repositories\Contracts\FeedbackRepository;

class FeedbackController extends BaseController
{
    protected $feedback;

    public function __construct(FeedbackRepository $feedback)
    {
        $this->feedback = $feedback;
    }

    public function index(Request $request)
    {
        $feedbacks = $this->feedback->latest()->get();

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('feedbacks'));
    }

    public function show(Request $request, $id)
    {
        $feedback = $this->feedback->find($id);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('feedback'));
    }
}
