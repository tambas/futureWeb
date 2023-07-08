<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public static $rules = [
        'store' => [
            'comment' => 'required|min:3|max:140',
        ]
    ];
}
