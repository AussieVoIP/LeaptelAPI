<?php

namespace Leaptel\API\Orders;

use Leaptel\API\APIBase;
use Leaptel\API\Response\CustomerOrder;

/** @package Leaptel\API */
class GetOrderByID extends APIBase
{
    protected string $retclass = CustomerOrder::class;
    protected string $indexby = "order_id";
    protected int $cacheforsecs = 86400;

    // Add a "timestamp" value to these objects
    protected string $addtimestamp = "timestamp";

    // Generate a hash of the raw result
    protected bool $addhash = true;

    public function __construct(string $orderid)
    {
        $this->path = "/orders/$orderid";
    }

    public function go(bool $refresh = false): CustomerOrder
    {
        return $this->getSingle($refresh);
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        print "This request is missing information - use /customers/customer_id/orders and filter.\n";
        var_dump($body);
        exit;
    }
}
