<?php

namespace App\Support\Form;

class SelectForm implements IForm
{
    public static function render($name, $field, $data, $params)
    {
        return view('support.form.select', [
            'name' => $name,
            'data' => $data,
            'field' => $field,
        ]);
    }
}
