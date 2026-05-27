<?php

namespace Leaptel\API\Customers;

use Leaptel\API\APIBase;
use Leaptel\API\Request\CustRequest;
use Override;

/** @package Leaptel\API */
class CustomerOrders extends APIBase
{
    protected int $customer_id;

    public function __construct(int $customer_id)
    {
        $this->customer_id = $customer_id;
        $this->path = '/customers/' . $this->customer_id . "/orders";
    }

    public function go()
    {
        print "Getting Customer Orders\n";
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        var_dump($body);
        exit;
    }
}
