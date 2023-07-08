<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuildEmblem extends Model
{
    protected $table = 'guilds_emblems';

    public $timestamps = false;

    public $server;
}
