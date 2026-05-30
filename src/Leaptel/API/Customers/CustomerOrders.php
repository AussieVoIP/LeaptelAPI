<?php

namespace Leaptel\API\Customers;

use Leaptel\API\APIBase;
use Leaptel\API\Response\CustomerOrder;
use Leaptel\Models\QueryCache;

/** @package Leaptel\API */
class CustomerOrders extends APIBase
{
    protected int $customer_id;
    // protected bool $showurl = true;

    protected string $retclass = CustomerOrder::class;
    protected string $indexby = "order_id";
    protected int $cacheforsecs = 86400;

    // Add a "timestamp" value to these objects
    protected string $addtimestamp = "timestamp";

    public function __construct(int $customer_id)
    {
        $this->customer_id = $customer_id;
        $this->path = '/customers/' . $this->customer_id . "/orders";
    }

    /** @return \Leaptel\API\Response\CustomerOrder[]  */
    public function go(bool $refresh = false): array
    {
        return $this->getMultipleNotPaginated($refresh);
    }
}
