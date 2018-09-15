<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Contracts\PostRepository;

class PostController extends Controller
{
    protected $post;

    public function __construct(PostRepository $post)
    {
        $this->post = $post;
    }

    public function index(Request $request)
    {
        $posts = $this->post
            ->latest()
            ->get();

        return response()->json($posts);
    }

    public function show($id)
    {
        $post = $this->post->find($id);

        return response()->json($post);
    }

    public function store()
    {

    }
}
