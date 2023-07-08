<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumKnownDevice extends Model
{
    protected $table = 'core_members_known_devices';

    protected $connection = 'forum';

    protected $primaryKey = 'device_key';

    public $timestamps = false;
}
