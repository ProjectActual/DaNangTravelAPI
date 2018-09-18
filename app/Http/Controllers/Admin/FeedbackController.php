<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
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

        return response()->json($feedbacks);
    }

    public function show(Request $request, $id)
    {
        $feedback = $this->feedback->find($id);

        return response()->json($feedback);
    }
}
