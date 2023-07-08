<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class EmailChecker
{
    public static function check($email)
    {
        $isEmailValid = false;

        if ($email) {
            try {
                $client = new Client();
                $res = $client->request('GET', "https://api.mailgun.net/v3/address/validate", [
                    'auth'    => [ 'api', config('dofus.mailgun_key') ],
                    'query'   => [ 'address' => $email ],
                    'timeout' => 10, // seconds
                ]);

                if ($res->getStatusCode() == 200) {
                    $json = json_decode((string)$res->getBody());

                    if (isset($json->is_valid)) {
                        $isEmailValid = $json->is_valid;
                    }
                }
            } catch (TransferException $e) {
                // continue
            }
        }

        return $isEmailValid;
    }
}
