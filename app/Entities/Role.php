<?php

namespace App\Entities;

use Zizaco\Entrust\EntrustRole;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use App\Entities\User;
use App\Entities\Permission;

/**
 * Class Role.
 *
 * @package namespace App\Entities;
 */
class Role extends EntrustRole implements Transformable
{
    use TransformableTrait;

    CONST NAME = [
        1 => 'ADMINISTRATOR',
        2 => 'CONGTACVIEN',
        3 => 'VIEWER',
    ];

    CONST CODE_NAME = [
        'ADMINISTRATOR'     => 1,
        'CONGTACVIEN'       => 2,
        'VIEWER'            => 3,
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
