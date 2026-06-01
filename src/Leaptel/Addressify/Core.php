<?php

namespace Leaptel\Addressify;

use GuzzleHttp\Client;
use Leaptel\Leaptel;

class Core
{
    public static ?Client $guz = null;

    protected bool $debug = false;

    public function setDebug(bool  $debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * This is exactly the same as APIBase::getGuzClient, but returns
     * a different client so they can be used simultaneously
     *
     * @return Client
     */
    public function getGuzClient(): Client
    {
        if (Core::$guz === null) {
            Core::$guz = new Client();
        }
        return Core::$guz;
    }

    public function getSecrets()
    {
        $secrets = Leaptel::getSecrets();
        return [
            "addressifyurl" => $secrets['addressifyurl'],
            "addressify_api_key" => $secrets['addressify_api_key'],
        ];
    }
}
