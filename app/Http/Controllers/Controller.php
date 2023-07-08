<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($message)
    {
        return response()->json(["message" => $message]);
    }

    public function error($code, $message, $errors = null)
    {
        if ($errors) {
            return response()->json(["message" => $message, "errors" => $errors], $code);
        } else {
            return response()->json(["message" => $message], $code);
        }
    }
}
