<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuildMember extends Model
{
    protected $table = 'guild_members';

    public $timestamps = false;

    public $server;

    public function character($server)
    {
        return ModelCustom::hasOneOnOneServer('world', $server, Character::class, 'Id', $this->CharacterId);
    }

    public function rankName()
    {
        $ranks = [
            0  => "A l'essai",
            1  => "Meneur",
            2  => "Bras Droit",
            3  => "Trésorier",
            4  => "Protecteur", 
            5  => "Artisan",
            6  => "Réserviste",
            7  => "Larbin",
            8  => "Gardien",
            9  => "Eclaireur",
            10  => "Espion",
            11  => "Diplomate",
            12 => "Secrétaire",
            13  => "Pénitent",
            14  => "Boulet",
            15  => "Déserteur",
            16  => "Bourreau",
            17  => "Apprenti",
            18  => "Marchand",
            19  => "Eleveur",
            20  => "Recruteur",
            21  => "Guide",
            22  => "Mentor",
            23  => "Elu",
            24  => "Conseiller",
            25  => "Muse",
            26  => "Gouverneur",
            27  => "Assassin",
            28  => "Initié",
            29  => "Voleur",
            30  => "Chercheur de trésors",
            31  => "Braconnier",
            32  => "Traître",
            33  => "Tueur de familiers",
            34  => "Mascotte",
            35  => "Tueur de Percepteur"
         ];
        
         return array_key_exists($this->RankId, $ranks) ? $ranks[$this->RankId] : "Rang inconnu";
    }
}
