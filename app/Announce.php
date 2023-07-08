<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announce extends Model
{
    protected $primaryKey = 'Id';

    protected $table = 'announces';

    public $timestamps = false;

    public $server;

    protected $fillable = ['Message'];

    public static $rules = [
        'store&update' => [
            'Message'               => 'required|min:3|max:200',
        ],
    ];

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }
}
