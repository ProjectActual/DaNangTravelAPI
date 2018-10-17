<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;

use App\Services\SendMail;
use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\FeedbackUpdateRequest;
use App\Repositories\Contracts\FeedbackRepository;
use App\Criteria\Feedback\FilterFeedBackWithCTVCriteria;

class FeedbackController extends BaseController
{
    protected $feedbackRepository;
    protected $paginate = 15;

    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;

        $this->feedbackRepository->pushCriteria(new FilterFeedBackWithCTVCriteria());
    }

    /**
     * Show all feedback
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $feedbacks = $this->feedbackRepository
            ->latest()
            ->paginate($this->paginate);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('feedbacks'));
    }

    /**
     * show detail of feedback
     * @param  int  $id      This is used to find the feedback details
     * @return Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $feedback = $this->feedbackRepository
            ->find($id);

        return $this->responses(trans('notication.load.success'), Response::HTTP_OK, compact('feedback'));
    }

    /**
     * delete a feedback with id
     * @param  int  $id      This is used to delete feedback
     * @return Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->feedbackRepository->delete($id);

        return $this->responses(trans('notication.delete.success'), Response::HTTP_OK);
    }

    /**
     * send feedback to user via email
     * @param  int  $id      This is used to delete feedback
     * @return Illuminate\Http\Response
     */
    public function send(FeedbackRequest $request)
    {
        $info = [
            'title'   => $request->title,
            'content' => $request->content,
        ];
        //send email feedback
        SendMail::send($request->email, trans('notication.feedback.subject'), 'email.feedback', $info);

        return $this->responses(trans('notication.feedback.success'), Response::HTTP_OK);
    }
}
