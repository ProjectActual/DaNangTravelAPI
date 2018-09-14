<?php

namespace App\Entities;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Entities\Post;
use App\Entities\Role;
use App\Entities\Comment;

/**
 * Class User.
 *
 * @package namespace App\Entities;
 */
class User extends Authenticatable implements Transformable
{
    use Notifiable;
    use HasApiTokens;
    use TransformableTrait;
    use EntrustUserTrait;

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
