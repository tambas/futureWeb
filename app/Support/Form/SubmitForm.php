<?php

namespace App\Support\Form;

class SubmitForm implements IForm
{
    public static function render($name, $field, $data, $params)
    {
        return view('support.form.submit', [
            'name' => $name,
            'data' => $data,
            'field' => $field,
        ]);
    }
}
