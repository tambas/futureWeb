<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Experience;
use \Cache;

class Alignment extends Model
{
    private $AlignmentSide;
    private $Honor;

    public function __construct($AlignmentSide, $Honor)
    {
        $this->AlignmentSide = $AlignmentSide;
        $this->Honor = $Honor;        
    }

    public function level()
    {
        if($this->AlignmentSide == 0)
            return "";
        $level = Cache::remember('alignment_level_'.$this->Honor, 1000, function () { 
            return Experience::where('AlignmentHonor', '<=', $this->Honor)->orderBy('Level', 'desc')->first();
        });

        if ($level) {
            return $level->Level;
        } else {
            return 1;
        }
    }

    public function sideFrench()
    {
        switch($this->AlignmentSide)
        {
            case 0:
                return "neutre";
            break;

            case 1:
                return "bonta";
            break;

            case 2:
                return "brakmar";
            break;

            default:
                return "neutre";
            break;
        }
    }

}
