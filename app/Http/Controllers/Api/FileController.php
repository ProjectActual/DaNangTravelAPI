<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Admin\Post\UploadFileRequest;

class FileController extends Controller
{
    /**
     * upload file image
     * @param  UploadFileRequest $request These are binding rules when requests are accepted
     * @return Illuminate\Http\Response
     */
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
}
