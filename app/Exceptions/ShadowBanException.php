<?php

namespace App\Exceptions;

use Exception;

class ShadowBanException extends Exception
{
    public function __construct()
    {
        parent::__construct("ShadowBan User");
    }
}
