<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public static $rules = [
        'attach&detach' => [
            'permission'       => 'required|numeric|exists:permissions,id',
        ],
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
