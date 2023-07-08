<?php

namespace App\Support;

use App\Support\Form\TextForm;
use App\Support\Form\FileForm;
use App\Support\Form\RadioForm;
use App\Support\Form\SelectForm;
use App\Support\Form\TextareaForm;
use App\Support\Form\AccountsForm;
use App\Support\Form\CharactersForm;
use App\Support\Form\ServersForm;
use App\Support\Form\SubmitForm;

class Support
{
    public static function generateForm($child, $params = false, $post = [])
    {
        $filePath = public_path() . "/support_files/$child.json";

        if (!file_exists($filePath)) {
            // TODO convert to view
            return "Choix invalide.";
        }

        $html = "";
        $json = json_decode(file_get_contents($filePath));
        
        foreach ($json->fields as $field) {
            $type = $field->type;
            $name = $field->name;
            $data = (isset($field->data) ? $field->data : false);

            $form = false;
            switch ($type) {
                case 'text':
                case 'email':
                case 'integer':
                    $form = new TextForm;
                    break;
                case 'file':
                    $form = new FileForm;
                    break;
                case 'radio':
                    $form = new RadioForm;
                    break;
                case 'select':
                    $form = new SelectForm;
                    break;
                case 'textarea':
                    $form = new TextareaForm;
                    break;
                case 'accounts':
                    $form = new AccountsForm;
                    break;
                case 'characters':
                    $form = new CharactersForm;
                    break;
                case 'servers':
                    $form = new ServersForm;
                    break;
                case 'submit':
                    $form = new SubmitForm;
                    break;
            }

            if ($form) {
                $html .= $form->render($name, $field, $data, $post);
            }
        }

        return $html;
    }
}
