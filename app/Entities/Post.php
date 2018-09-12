<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use App\Entities\Tag;
use App\Entities\User;
use App\Entities\Comment;
use App\Entities\Category;

/**
 * Class Post.
 *
 * @package namespace App\Entities;
 */
class Post extends Model implements Transformable
{
    use TransformableTrait;

    CONST DISPLAY = [
        'YES'    => 'YES',
        'NO'     => 'NO',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table     = 'posts';

    protected $fillable  = [
        'title', 'uri_post', 'content', 'display', 'count_view', 'user_id',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id')
            ->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Tag::class, 'post_category', 'post_id', 'category_id')
            >withTimestamps();
    }

}
