<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use App\Entities\User;
use App\Entities\Permission;

/**
 * Class Role.
 *
 * @package namespace App\Entities;
 */
class Role extends Model implements Transformable
{
    use TransformableTrait;

    CONST NAME = [
        'ADMINISTRATOR' => 'ADMINISTRATOR',
        'CONGTACVIEN'   => 'CONGTACVIEN',
        'VIEWER'        => 'VIEWER',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table     = 'roles';

    protected $fillable  = [
        'name', 'display_name', 'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')
            ->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id')
            ->withTimestamps();
    }
}
