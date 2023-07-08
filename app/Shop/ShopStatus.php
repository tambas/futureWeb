<?php

namespace App\Shop;

class ShopStatus
{
    const PAYMENT_SUCCESS = 0;
    const PAYMENT_FAIL    = 1;
    const PAYMENT_ERROR   = 2;
    const IN_PROGRESS     = 3;

    public static function getState($state)
    {
        switch ($state) {
            case 0:
                return 'Success';
            case 1:
                return 'Failed';
            case 2:
                return 'Error';
            case 3:
                return 'In progress';
        }
    }
}
