<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'task',
        'status',
        'color',
        'description',
        'status_order'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
