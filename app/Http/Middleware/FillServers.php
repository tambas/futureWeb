<?php

namespace App\Http\Middleware;

use Closure;
use \Config;

class FillServers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $json = json_decode(file_get_contents(base_path() . "/servers.json"));

        $servers = [];
        $details = [];

        foreach ($json->servers as $server) {
            $data = new \stdClass;
            $data->name     = $server->name;
            $data->version  = $server->version;
            $data->ip       = $server->ip;
            $data->port     = $server->port;
            $data->desc     = $server->desc;
            $data->ogrine   = $server->ogrine;
            $data->rpg      = $server->rpg;
            $data->prestige = $server->prestige;

            $servers[] = $server->name;
            $details[$server->name] = $data;

            foreach (['auth', 'world', 'data'] as $db) {
                $db_var = "db_$db";

                $database = [
                    'driver'    => 'mysql',
                    'host'      => $server->db_host,
                    'database'  => $server->$db_var,
                    'username'  => $server->db_user,
                    'password'  => $server->db_pass,
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => '',
                    'strict'    => false,
                ];

                Config::set("database.connections.{$server->name}_$db", $database);
            }
        }

        Config::set('dofus.servers', $servers);
        Config::set('dofus.details', $details);

        return $next($request);
    }
}
