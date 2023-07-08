<?php

namespace App\Support\Form;

class ServersForm implements IForm
{
    public static function render($name, $field, $data, $params)
    {
        $servers = config('dofus.details');

        return view('support.form.servers', [
            'name'    => $name,
            'data'    => $data,
            'child'   => isset($field->child) ? $field->child : false,
            'servers' => $servers,
            'field'   => $field,
        ]);
    }
}
