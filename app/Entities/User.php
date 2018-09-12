<?php

namespace App\Entities;

use Illuminate\Notifications\Notifiable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Entities\Post;
use App\Entities\Role;
use App\Entities\Comment;
use App\Entities\Category;
use App\Entities\Feedback;

/**
 * Class User.
 *
 * @package namespace App\Entities;
 */
class User extends Authenticatable implements Transformable
{
    use Notifiable;
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

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
        ->withTimestamps();
    }

}
