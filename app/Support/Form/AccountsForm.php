<?php

namespace App\Support\Form;

use App\Account;
use Auth;

class AccountsForm implements IForm
{
    public static function render($name, $field, $data, $params)
    {
        $server = isset($params['server']) ? $params['server'] : 'sigma';

        $accounts = Auth::user()->accounts($server);

        if (count($accounts) > 0) {
            return view('support.form.accounts', [
                'name'     => $name,
                'child'    => isset($field->child) ? $field->child : false,
                'accounts' => $accounts,
                'data'     => $data,
                'field'    => $field,
            ]);
        }

        return "<div class='alert alert-danger'>Vous n'avez aucun compte de jeu.</div>";
    }
}
