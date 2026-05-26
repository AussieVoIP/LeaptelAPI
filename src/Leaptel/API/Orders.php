<?php

namespace Leaptel\API;

/** @package Leaptel\API */
class Orders extends APIBase
{
    protected string $path = '/orders';

    public function go(): array
    {
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        print "Incomplete\n";
        var_dump($body);
        exit;
    }
}
