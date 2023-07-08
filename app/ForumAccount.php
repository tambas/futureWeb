<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumAccount extends Model
{
    protected $table = 'core_members';

    protected $connection = 'forum';

    protected $primaryKey = 'member_id';

    public $timestamps = false;

    public function generateSalt()
    {
        $salt = '';

        for ($i=0; $i<22; $i++) {
            do {
                $chr = rand(48, 122);
            } while (in_array($chr, range(58, 64)) or in_array($chr, range(91, 96)));

            $salt .= chr($chr);
        }

        return $salt;
    }

    public function encryptedPassword($password)
    {
        return crypt($password, '$2a$13$' . $this->members_pass_salt);
    }
}
