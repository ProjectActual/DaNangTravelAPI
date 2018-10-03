<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Admin\Post\UploadFileRequest;

class FileController extends Controller
{
    public function uploadFile(UploadFileRequest $request)
    {
        $path = $request->fileUpload->store('public/images/avatar_post');

        $url = "http://$_SERVER[HTTP_HOST]" . Storage::url($path);
        return response()->json($url, 200);
    }
}
