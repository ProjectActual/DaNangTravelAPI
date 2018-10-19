<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller
{
    /**
     * gía trị được cho là hằng số vì không thay đổi theo thời gian.
     */
    CONST TYPE = [
        'EXPIRED'   => 'expired',
    ];

    /**
     * hàm parse ra kết quả Json
     *
     * @param  string $message tin nhắn thành công của object
     * @param  int $status  status code của object
     * @param  array  $data    Dữ liệu được truyền vào để đưa xuống Client, nullable
     * @return Illuminate\Http\Response
     */
    public function responses($message, $status, $data = [])
    {
        if($status > 102  && $status <= 202) {
            return response()->json(compact('message', 'status', 'data'), $status);
        } else {
            $error = true;
            return response()->json(compact('message', 'status'), $status);
        }
    }

    /**
     * hàm parse ra lỗi validator exception của laravel
     *
     * @param  string $key  gía trị khóa của lỗi
     * @param  string $message   mô tả ngắn về lỗi
     * @return Illuminate\Http\Response
     */
    public function responseErrors($key, $message)
    {
        throw \Illuminate\Validation\ValidationException::withMessages([$key => $message]);
    }

    /**
     * hàm parse ra lỗi exception của laravel
     *
     * @param  string $message   mô tả ngắn về lỗi
     * @param  int $status  status code của lỗi
     * @param  string $type    loại dữ liệu
     * @return Illuminate\Http\Response
     */
    public function responseException($message, $status, $type)
    {
        return response()->json([
            'message'     => $message,
            'status'      => $status,
            'type'        => $type
        ], $status);
    }
}
