<?php

namespace App\Http\Middleware;

use Closure;

use App\Entities\Post;

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

    public function handle(array $arrPost)
    {
        $post = Post::find($arrPost['id']);

        if (!$this->isPostViewed($post))
        {
            $post->increment('count_view');
            $this->storePost($post);
        }
    }

    private function isPostViewed($post)
    {
        $viewed = session('viewed_posts', []);

        return array_key_exists($post->id, $viewed);
    }

    private function storePost($post)
    {
        $key = 'viewed_posts.' . $post->id;

        session([$key => time()]);
    }
}
