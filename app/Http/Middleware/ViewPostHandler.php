<?php

namespace App\Http\Middleware;

use Closure;

use App\Entities\Post;
use DB;

class ViewPostHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $session;

    /**
     * Hàm xử lí khi event được nhắc đến
     *
     * @param $id id của bài viết
     * @return void
     */
    public function handle($id)
    {
        $post_id = $id;
        $session_name = request()->session_name;

        // khi event được nhắc đến thì ta kiểm tra view_count xem đã tồn tại sesion_name ,
        // nếu tồn tại thì không tăng view, nếu không tồn tại thì tăng view

        $isExists = DB::table('view_count')->where(compact('post_id', 'session_name'))->count();

        if (!$isExists) {
            DB::table('view_count')->insert(compact('post_id', 'session_name'));
        }

        $viewCount = DB::table('view_count')->where('post_id', $id)->count();
        $post = Post::find($id);
        $post->count_view = $viewCount;
        $post->save();
    }
}
