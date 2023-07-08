<?php

//namespace SumanIon;
namespace App\Helpers;

use Closure;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class CloudFlare
{
    /**
     * List of IP's used by CloudFlare.
     * @var array
     */
    protected static $ips = [
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/12',
        '108.162.192.0/18',
        '131.0.72.0/22',
        '141.101.64.0/18',
        '162.158.0.0/15',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '188.114.96.0/20',
        '190.93.240.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '199.27.128.0/21',
        '2400:cb00::/32',
        '2405:8100::/32',
        '2405:b500::/32',
        '2606:4700::/32',
        '2803:f800::/32',
    ];

    /**
     * Checks if current request is coming from CloudFlare servers.
     *
     * @return bool
     */
    public static function isTrustedRequest():bool
    {
        return IpUtils::checkIp(Request::ip(), static::$ips);
    }

    /**
     * Executes a callback on a trusted request.
     *
     * @param  Closure $callback
     *
     * @return mixed
     */
    public static function onTrustedRequest(Closure $callback)
    {
        //if (static::isTrustedRequest()) {
            return $callback();
        //}
    }

    /**
     * Determines "the real" IP address from the current request.
     *
     * @return string
     */
    public static function ip():string
    {
        return static::onTrustedRequest(function () {
            return filter_var(Request::header('CF_CONNECTING_IP'), FILTER_VALIDATE_IP);
        }) ?: Request::ip();
    }

    /**
     * Determines country from the current request.
     *
     * @return string
     */
    public static function country():string
    {
        return static::onTrustedRequest(function () {
            return Request::header('CF_IPCOUNTRY');
        }) ?: '';
    }
}
