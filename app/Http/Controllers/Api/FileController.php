<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Admin\Post\UploadFileRequest;
use App\Repositories\Contracts\PostRepository;
use App\Http\Controllers\BaseController;

class FileController extends BaseController
{
    /**
     * upload file image
     * @param  UploadFileRequest $request These are binding rules when requests are accepted
     * @return Illuminate\Http\Response
     */
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function uploadFile(UploadFileRequest $request)
    {
        if(empty($request->fileUpload)) {
            $url = '';
        } else {
            //create category and link image
            $path = $request->fileUpload->store('public/images/avatar_post');
            $url = "http://$_SERVER[HTTP_HOST]" . Storage::url($path);
        }

        return response()->json($url, 200);
    }

    public function convertAvatarPost(Request $request)
    {
        $posts = $this->postRepository->skipPresenter()->all();
        foreach($posts as $post) {
            $hostNameOld     = parse_url($post->avatar_post, PHP_URL_HOST);
            $hostNameCurrent = $_SERVER['HTTP_HOST'];
            if ($hostNameOld == $hostNameCurrent) {
                continue;
            }
            if (empty($port = parse_url($post->avatar_post, PHP_URL_PORT))) {
                $avatar_post = str_replace($hostNameOld, $hostNameCurrent, $post->avatar_post);
            } else {
                $avatar_post = str_replace("{$hostNameOld}:{$port}", $hostNameCurrent, $post->avatar_post);
            }
            $post->avatar_post = $avatar_post;
            $post->save();
        }
        return $this->responses('Convert dữ liệu thành công', 200);
    }
}
