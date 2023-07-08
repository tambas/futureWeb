<?php

namespace App;

use App\Account;
use Auth;
use Illuminate\Database\Eloquent\Model;

class World extends Model
{
    protected $primaryKey = 'Id';
    
    public $timestamps = false;

    public function isOnline()
    {
        return $this->Status == 3 ? true : false;
    }

    public function getOnlineCharacters()
    {
        return $this->CharsCount;
    }

    public static function isServerExist($server)
    {
        if (!in_array($server, config('dofus.servers'))) {
            return false;
        }
        return true;
    }

    public static function isCharacterOwnedByMe($server, $accountId, $characterId)
    {
        $account = Account::on($server . '_auth')->where('Id', $accountId)->where('Email', Auth::user()->email)->first();

        if ($account) {
            $account->server = $server;
            $characters = $account->characters(true);

            if ($characters) {
                foreach ($characters as $character) {
                    if ($character && $characterId == $character->Id) {
                        return true;
                    }
                }

                return false;
            }
        }
    }

    public static function isCharacterDeletedOwnedByMe($server, $accountId, $characterId)
    {
        $account = Account::on($server . '_auth')->where('Id', $accountId)->where('Email', Auth::user()->email)->first();
        if ($account) {
            $account->server = $server;
            $characters = $account->DeletedCharacters(true);
            if ($characters) {
                foreach ($characters as $character) {
                    if ($characterId == $character->Id) {
                        return true;
                    }
                }
                return false;
            }
        }
    }
}
