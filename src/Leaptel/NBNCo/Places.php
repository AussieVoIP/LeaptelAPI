<?php

namespace Leaptel\NBNCo;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Leaptel\Models\PlacesDetail;
use Leaptel\NBNCo\Objects\LocDetails;
use Leaptel\NBNCo\Objects\Suggestion;

/**
 * Taken from https://github.com/LukePrior/nbn-upgrade-map/blob/main/code/nbn.py
 * which manually asks places.nbnco.net.au for the authoritive lookup results.
 *
 * @package Leaptel\NBNCo
 */
class Places
{
    protected static ?CookieJar $jar = null;
    protected static ?Client $guz = null;
    protected static bool $debug = false;
    protected static array $headers = [
        "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0",
        "Origin" => "https://www.nbnco.com.au",
        "Referer" => "https://www.nbnco.com.au/",
    ];

    public static function setDebug(bool $debug = true)
    {
        self::$debug = $debug;
    }

    /**
     * This is exactly the same as APIBase::getGuzClient, but returns
     * a different client so they can be used simultaneously
     *
     * @return Client
     */
    protected static function getGuzClient(): Client
    {
        if (Places::$guz === null) {
            Places::$jar = new CookieJar();
            Places::$guz = new Client(["http_errors" => false, "cookies" => Places::$jar, "headers" => self::$headers]);
            Places::$guz->request("GET", "https://www.nbnco.com.au");
        }
        return Places::$guz;
    }

    public static function getAutoComplete(string $req)
    {
        if (strlen($req) < 4) {
            return [];
        }
        $c = self::getGuzClient();
        $url = "https://places.nbnco.net.au/places/v1/autocomplete";
        $params = [
            "query" => ["query" => $req, "timestamp" => str_replace(".", "", microtime(true))],
        ];
        if (self::$debug) {
            $params["debug"] = true;
        }
        $res = $c->request("GET", $url, $params);
        $resp = json_decode($res->getBody(), true);
        $source = $resp['source'];
        $retarr = [];
        foreach ($resp['suggestions'] as $s) {
            $s = new Suggestion($s);
            $s->source = $source;
            $retarr[] = $s;
        }
        return $retarr;
    }

    public static function getPlacesDetail(string $locid): PlacesDetail
    {
        if (!preg_match("/^LOC\d+$/", $locid)) {
            throw new \Exception("invalid location id");
        }
        $m = PlacesDetail::where("location_id", $locid)->first();
        if (!$m) {
            $c = self::getGuzClient();
            $url = "https://places.nbnco.net.au/places/v2/details/$locid";
            $params = [
                "query" => ["timestamp" => str_replace(".", "", microtime(true))],
            ];
            $res = $c->request("GET", $url, $params);
            $resp = json_decode($res->getBody(), true);
            $m = LocDetails::getPlacesDetail($resp);
        }
        return $m;
    }
}
