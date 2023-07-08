<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lang extends Model
{
    protected $table = 'langs';

    protected $connection = 'sigma_world';

    public $timestamps = false;
}
