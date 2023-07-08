<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7;

use App\Transfert;
use App\World;

use Auth;

class Stump
{
    public static function transfert($server, $accountId, $type, $amount, $url, $process, $fallback)
    {
        $world = World::on($server . '_auth')->where('Name', strtoupper($server))->first();

        if (!$world || !$world->isOnline()) {
            return false;
        }

        $transfert = new Transfert;
        $transfert->user_id    = Auth::user()->id;
        $transfert->account_id = $accountId;
        $transfert->server     = $server;
        $transfert->state      = Transfert::IN_PROGRESS;
        $transfert->amount     = $amount;
        $transfert->type       = $type;
        $transfert->save();

        $api = config('dofus.details')[$server];
        $success = false;

        $process();

        try {
            $client = new Client();
            $res = $client->request('PUT', "http://{$api->ip}:{$api->port}$url", [
                'headers' => [
                    'APIKey' => config('dofus.api_key')
                ],
                'timeout' => 20, // seconds
            ]);

            if ($res->getStatusCode() == 200) {
                // Server return 200 (Good)

                $transfert->state  = Transfert::OK_API;
                //$transfert->rawIn  = Psr7\str($res->getRequest());
                //$transfert->rawOut = Psr7\str($res->getBody());
                $transfert->save();

                $success = true;
            } else {
                // Server return 2xx (Bad)
                $transfert->state  = Transfert::REFUND;
                //$transfert->rawIn  = Psr7\str($res->getRequest());
                //$transfert->rawOut = Psr7\str($res->getBody());
                $transfert->save();

                $fallback();

                $success = false;
            }
        } catch (ServerException $e) {
            // 5xx errors

            $fallback();

            $transfert->state  = Transfert::REFUND;
            $transfert->rawIn  = Psr7\str($e->getRequest());

            if ($e->hasResponse()) {
                $transfert->rawOut = Psr7\str($e->getResponse());
            } else {
                $transfert->rawOut = "NO RESPONSE";
            }

            $transfert->save();

            $success = false;
        } catch (TransferException $e) {
            // Other errors (timeout?)

            $fallback();

            $transfert->state  = Transfert::REFUND;
            $transfert->rawIn  = Psr7\str($e->getRequest());

            if ($e->hasResponse()) {
                $transfert->rawOut = Psr7\str($e->getResponse());
            } else {
                $transfert->rawOut = "NO RESPONSE";
            }

            $transfert->save();

            $success = false;
        }

        return $success;
    }

    public static function get($server, $url)
    {
        $world = World::on($server . '_auth')->where('Name', strtoupper($server))->first();

        if (!$world || !$world->isOnline()) {
            return false;
        }

        $api = config('dofus.details')[$server];
        $success = false;

        try {
            $client = new Client();
            $res = $client->request('GET', "http://{$api->ip}:{$api->port}$url", [
                'headers' => [
                    'APIKey' => config('dofus.api_key')
                ],
                'timeout' => 10, // seconds
            ]);

            if ($res->getStatusCode() == 200) {
                // Server return 200 (Good)
               return $res->getBody();
            } else {
                // Server return 2xx (Bad)
                $success = false;
            }
        } catch (ServerException $e) {
            // 5xx errors
            $success = false;
        } catch (TransferException $e) {
            // Other errors (timeout?)
            $success = false;
        }

        return $success;
    }
}
