<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Services\DofusForge;

class ItemTemplate extends Model
{
    protected $table = 'items_templates';

    protected $connection = 'sigma_world';

    public $timestamps = false;

    public static $rules = [
        'getItemById' => [
            'itemid'               => 'required|numeric|digits_between:1,5',
        ],
    ];

    public function name($server = null)
    {
        return DofusForge::text($this->NameId, $server);
    }

    public function description($server = null)
    {
        return DofusForge::text($this->DescriptionId, $server);
    }

    public function image()
    {
        return DofusForge::asset('dofus/www/game/items/200/' . $this->IconId . '.png');
    }
}
