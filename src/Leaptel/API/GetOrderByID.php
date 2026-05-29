<?php

namespace Leaptel\API;

/** @package Leaptel\API */
class GetOrderByID extends APIBase
{
    public function __construct(string $orderid)
    {
        $this->path = "/orders/$orderid";
    }

    public function go(): array
    {
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        print "This request is missing information - use /customers/customer_id/orders and filter.\n";
        var_dump($body);
        exit;
    }
}
