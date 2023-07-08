<?php

namespace App\Services;

abstract class Payment
{
    abstract protected function rates();

    abstract protected function palier($country, $method, $palier);

    abstract protected function check($country, $method, $palier, $code);
}
