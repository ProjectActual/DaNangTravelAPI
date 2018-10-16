<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ViewCount extends Model
{
    protected $table     = 'view_count';

    protected $fillable = [
        'session_name', 'post_id',
    ];
}
