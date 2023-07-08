<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumAccountValidating extends Model
{
    protected $table = 'core_validating';

    protected $connection = 'forum';

    protected $primaryKey = 'vid';

    public $timestamps = false;
}
