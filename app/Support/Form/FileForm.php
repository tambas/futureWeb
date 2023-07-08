<?php

namespace App\Support\Form;

class FileForm implements IForm
{
    public static function render($name, $field, $data, $params)
    {
        return view('support.form.file', [
            'name'   => $name,
            'field'  => $field,
            'data'   => $data,
        ]);
    }
}
