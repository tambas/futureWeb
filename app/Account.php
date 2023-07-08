<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use \Cache;

use App\WorldCharacter;
use App\ModelCustom;
use App\Transfert;

class Account extends Model
{
    protected $primaryKey = 'Id';

    protected $table = 'accounts';

    public $timestamps = false;

    public $server;

    protected $dates = ['CreationDate', 'BanEndDate', 'LastConnection'];

    protected $hidden = ['PasswordHash', 'Salt'];

    protected $fillable = [
        'Login',
        'PasswordHash',
        'Salt',
        'Nickname',
        'UserGroupId',
        'Ticket',
        'SecretQuestion',
        'SecretAnswer',
        'Lang',
        'Email',
        'CreationDate',
        'SubscriptionEnd',
        'LastVote',
        'VoteCount',
        'IsJailed',
        'IsBanned',
    ];

    public static $rules = [
        'sanction' => [
            'BanEndDate'            => 'date_format:Y-m-d H:i:s',
            'BanReason'             => 'required',
        ],
        'register' => [
            'login'                => 'required|min:3|max:32|regex:/(^[A-Za-z0-9-_]+$)+/|unique:{DB}.accounts,Login',
            'nickname'             => 'required|min:3|max:32|regex:/(^[A-Za-z0-9-_]+$)+/|unique:{DB}.accounts,Nickname',
            'password'             => 'required|min:6',
            'passwordConfirmation' => 'required|same:password',
        ],
        'update-password' => [
            'password'             => 'required|min:6',
            'passwordConfirmation' => 'required|same:password',
        ],
        'admin-update-password' => [
            'password'             => 'required|min:6',
            'passwordConfirmation' => 'required|same:password',
        ],
    ];

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }

    public function isStaff()
    {
        if ($this->UserGroupId > 1) {
            return true;
        } else {
            return false;
        }
    }

    public function isBanned()
    {
        if ($this->IsBanned == '1' && ($this->BanEndDate > Carbon::now() || $this->BanEndDate == null)) {
            return true;
        } else {
            return false;
        }
    }

    public function characters($nocache = null, $minimal = null)
    {
        if (!$nocache) {
            $characters = Cache::remember('characters_'.$this->server.'_'.$this->Id.'_'.$minimal, 10, function () use ($minimal) {
                $characters = [];
                $worldCharacters = ModelCustom::hasManyOnOneServer('auth', $this->server, WorldCharacter::class, 'AccountId', $this->Id);

                foreach ($worldCharacters as $worldCharacter) {
                        if ($worldCharacter->character() && $worldCharacter->character()->DeletedDate == null) {
                            if($minimal)
                            {
                                if(($worldCharacter->character()->LastUsage > Carbon::today()->subMonths(6)->toDateString()) && ($worldCharacter->character()->level($this->server) >= 20 || $worldCharacter->character()->PrestigeRank > 0))
                                     $characters[] = $worldCharacter->character();
                            }
                            else
                                $characters[] = $worldCharacter->character();
                        }
                    
                }
                return $characters;
            });
        } else {
            $characters = [];
            $worldCharacters = ModelCustom::hasManyOnOneServer('auth', $this->server, WorldCharacter::class, 'AccountId', $this->Id);

            foreach ($worldCharacters as $worldCharacter) {
                    if ($worldCharacter->character() && $worldCharacter->character()->DeletedDate == null) {
                       if($minimal)
                            {
                                if(($worldCharacter->character()->LastUsage > Carbon::today()->subMonths(6)) && ($worldCharacter->character()->level($this->server) >= 20 || $worldCharacter->character()->PrestigeRank > 0))
                                     $characters[] = $worldCharacter->character();
                            }
                            else
                                $characters[] = $worldCharacter->character();
                    }
            }
            return $characters;
        }
        return $characters;
    }

    public function DeletedCharacters($nocache = null)
    {
        if (!$nocache) {
            $characters = Cache::remember('characters_deleted_'.$this->server.'_'.$this->Id, 0.4, function () {
                $characters = [];
                $worldCharacters = ModelCustom::hasManyOnOneServer('auth', $this->server, WorldCharacter::class, 'AccountId', $this->Id);

                foreach ($worldCharacters as $worldCharacter) {
                    if ($worldCharacter->character() && $worldCharacter->character()->DeletedDate && !MarketCharacter::inSell($worldCharacter->character())) {
                        $characters[] = $worldCharacter->character();
                    }
                }

                return $characters;
            });
        } else {
            $characters = [];
            $worldCharacters = ModelCustom::hasManyOnOneServer('auth', $this->server, WorldCharacter::class, 'AccountId', $this->Id);

            foreach ($worldCharacters as $worldCharacter) {
                if ($worldCharacter->character() && $worldCharacter->character()->DeletedDate && !MarketCharacter::inSell($worldCharacter->character())) {
                    $characters[] = $worldCharacter->character();
                }
            }

            return $characters;
        }

        return $characters;
    }

    public function transferts($take = null)
    {
        $transferts = null;

        if ($take) {
            $transferts = Cache::remember('transferts_' . $this->server . '_' . $this->Id . '_' . $take, 10, function () use ($take) {
                return Transfert::where('server', $this->server)->where('account_id', $this->Id)->orderBy('created_at', 'desc')->take($take)->get();
            });
        } else {
            $transferts = Cache::remember('transferts_' . $this->server . '_' . $this->Id, 10, function () {
                return Transfert::where('server', $this->server)->where('account_id', $this->Id)->orderBy('created_at', 'desc')->get();
            });
        }

        return $transferts;
    }

    public function points()
    {
        $account = Account::on($this->server . '_world')->where('Id', $this->Id)->first();

        if ($account) {
            return $account->Tokens + $account->NewTokens;
        } else {
            return 0;
        }
    }

    public function addPoints($amount)
    {
        $account = Account::on($this->server . '_world')->where('Id', $this->Id)->first();

        if ($account) {
            $account->NewTokens += $amount;
            $account->save();

            return true;
        }

        return false;
    }

    public function htmlStatus()
    {
        $bannerAccountLogin = '';
        $texts = [];
        $hidden = '';
        if ($this->IsJailed == 1) {
            $texts[] = 'Jailed';
            $label = 'danger';
        }
        if ($this->IsBanned == 1) {
            $texts[] = 'Banned';
            $label = 'danger';
        }
        if ($this->IsJailed == 0 && $this->IsBanned == 0) {
            $texts[] = 'OK';
            $label = 'success';
            $hidden = 'hidden';
        }
        if ($this->IsJailed == 1 || $this->IsBanned == 1) {
            $bannerAccount = $this->findOrFail($this->BannerAccountId);
            if ($bannerAccount) {
                $bannerAccountLogin = $bannerAccount->Login;
            } else {
                $bannerAccountLogin = 'Unknown';
            }
            if ($this->BanEndDate) {
                $texts[] .= '('.$this->BanEndDate.')';
            } else {
                $texts[] .= '()';
            }
        }

        $span = '';
        foreach ($texts as $text) {
            $span .= ' <span class="label label-'.$label.'">'.$text.'</span>';
        }

        $span .= ' <button id="pop-'.$this->Id.'" class="'.$hidden.' btn btn-xs btn-default" data-toggle="popover" data-placement="top" title="Sanctioned by '.$bannerAccountLogin.'" data-content="'.$this->BanReason.'"><i class="fa fa-info"></i></button>';


        return $span;
    }

    public function user()
    {
        return User::where('email', $this->Email)->first();
    }

    public function availableSlots()
    {
        return (config('dofus.characters_limit') - count($this->characters(true,false)) > 0) ? (config('dofus.characters_limit') - count($this->characters(true,false))) : 0;
    }
}
