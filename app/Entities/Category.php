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

    protected $fillable  = [
        'name_category', 'description', 'uri_category', 'type_category'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id', 'id');
    }

}
