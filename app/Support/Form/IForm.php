<?php

namespace App\Support\Form;

interface IForm
{
    public static function render($name, $field, $data, $params);
}
