<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use App\Entities\User;

/**
 * Class Feedback.
 *
 * @package namespace App\Entities;
 */
class Feedback extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table     = 'feedbacks';

    protected $fillable  = [
        'title', 'content', 'email', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
