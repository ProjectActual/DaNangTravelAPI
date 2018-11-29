<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Prettus\Repository\Contracts\Presentable;
use Prettus\Repository\Traits\PresentableTrait;

use App\Entities\Tag;
use App\Entities\User;
use App\Entities\Comment;
use App\Entities\Category;
use App\Entities\PostImage;

/**
 * Class Post.
 *
 * @package namespace App\Entities;
 */
class Post extends Model implements Transformable, Presentable
{
    use TransformableTrait;
    use PresentableTrait;

    CONST STATUS = [
        'ACTIVE'    => 'ACTIVE',
        'INACTIVE'  => 'INACTIVE',
    ];

    CONST IS_HOT = [
        'YES'    => 'YES',
        'NO'     => 'NO',
    ];

    CONST IS_SLIDER = [
        'YES'    => 'YES',
        'NO'     => 'NO',
    ];

    CONST CODE_CATEGORY = [
        'DU_LICH'     => 1,
        'DIEM_DEN'    => 2,
        'SU_KIEN'     => 3,
        'AM_THUC'     => 4,
        'CAM_NANG'    => 5,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table     = 'posts';

    protected $fillable  = [
        'title', 'uri_post', 'content', 'display', 'count_view',
        'avatar_post', 'user_id', 'category_id', 'status', 'is_hot', 'is_slider',
        'summary', 'created_at'
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post_images()
    {
        return $this->hasMany(PostImage::class, 'post_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id')
            ->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getAvatarPostAttribute($value)
    {
        if (empty($value)) {
            return "http://{$_SERVER['HTTP_HOST']}/images/users/travel-health.jpg";
        }

        return $value;
    }
}
