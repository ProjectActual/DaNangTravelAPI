<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Prettus\Repository\Contracts\Presentable;
use Prettus\Repository\Traits\PresentableTrait;

use App\Entities\Post;

/**
* Class Tag.
*
* @package namespace App\Entities;
*/
class Tag extends Model implements Transformable, Presentable
{
    use TransformableTrait;
    use PresentableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table     = 'tags';

    protected $fillable  = [
        'tag', 'uri_tag',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'tag_id', 'post_id')
            ->withTimestamps();
    }
}
