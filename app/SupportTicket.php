<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class SupportTicket extends Model
{
    protected $table = 'support_tickets';

    public static $rules = [
        'postMessage' => [
            'message' => 'required|between:10,5000',
        ],
        'postMessageAdmin' => [
            'message' => 'required',
        ]
    ];
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function request()
    {
        return $this->hasOne(SupportRequest::class, 'id', 'request_id');
    }

    public function isInfo()
    {
        return ($this->reply == 2);
    }
}
