<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller
{
    public function responses($message, $status, $data = [])
    {
        if($status > 102  && $status <= 202) {
            return response()->json(compact('message', 'status', 'data'), $status);
        } else {
            $error = true;
            return response()->json(compact('message', 'status'), $status);
        }
    }

    public function responseErrors($key, $message)
    {
        throw \Illuminate\Validation\ValidationException::withMessages([$key => $message]);
    }

    public function saveImage($file ,$uri = 'public/images/general')
    {
        $path = $file->store($uri);

        return $path;
    }
}
