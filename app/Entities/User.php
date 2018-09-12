<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use App\Entities\Post;
use App\Entities\Comment;
use App\Entities\Category;
use App\Entities\Feedback;

/**
 * Class User.
 *
 * @package namespace App\Entities;
 */
class User extends Model implements Transformable
{
    use TransformableTrait;

    CONST GENDER = [
        'MALE'    => 'MALE',
        'FEMALE'  => 'FEMALE',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table     = 'users';

    protected $fillable  = [
        'email', 'password', 'first_name', 'last_name', 'cmnd',
        'phone', 'avatar', 'gender', 'date_of_birth',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class, 'user_id', 'id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'user_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

}
