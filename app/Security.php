<?php

namespace App;

class Security
{
    public static function hash($algo, $text, $rounds = 1)
    {
        for ($i = 0; $i < $rounds; $i++) {
            $text = hash($algo, $text);
        }

        return $text;
    }
}
