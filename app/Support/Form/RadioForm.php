<?php

namespace App\Support\Form;

class RadioForm implements IForm
{
    public static function render($name, $field, $data, $params)
    {
        return view('support.form.radio', [
            'name' => $name,
            'data' => $data,
            'field' => $field,
        ]);
    }
}
