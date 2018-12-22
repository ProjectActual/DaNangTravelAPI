<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use App\Entities\User;
use App\Entities\Post;

/**
 * Class Category.
 *
 * @package namespace App\Entities;
 */
class Category extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table     = 'categories';

    CONST STATUS = [
        1 => 'ACTIVE',
        2 => 'INACTIVE'
    ];

    protected $fillable  = [
        'name_category', 'description', 'uri_category', 'type_category', 'status',
    ];

    CONST CODE_CATEGORY = [
        'DU_LICH'     => 1,
        'DIEM_DEN'    => 2,
        'SU_KIEN'     => 3,
        'AM_THUC'     => 4,
        'CAM_NANG'    => 5,
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id', 'id');
    }

}
