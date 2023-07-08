<?php

namespace App\Support\Form;

class TextareaForm implements IForm
{
    public static function render($name, $field, $data, $params)
    {
        return view('support.form.textarea', [
            'name' => $name,
            'data' => $data,
            'field' => $field,
        ]);
    }
}
