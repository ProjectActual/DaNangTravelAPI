<?php

namespace App\Entities;

use Zizaco\Entrust\EntrustPermission;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use App\Entities\Role;

/**
 * Class Permission.
 *
 * @package namespace App\Entities;
 */
class Permission extends EntrustPermission implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table     = 'permissions';

    protected $fillable  = [
        'name', 'display_name', 'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id')
            ->withTimestamps();
    }

}
