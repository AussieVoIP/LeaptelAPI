<?php

namespace Leaptel\API;

use Leaptel\API\Response\CustomerOrder;

/**
 * All currently open orders
 *
 * @package Leaptel\API
 */
class Orders extends APIBase
{
    protected string $path = '/orders';

    protected string $retclass = CustomerOrder::class;
    protected string $indexby = "order_id";
    protected int $cacheforsecs = 30;

    // Add a "timestamp" value to these objects
    protected string $addtimestamp = "timestamp";

    // Generate a hash of the raw result
    protected bool $addhash = true;

    /**
     * @param bool $refresh Bypass the query cache
     * @return array<int, CustomerOrder> Indexed by order_id
     */
    public function go(bool $refresh = false): array
    {
        return $this->getMultipleNotPaginated($refresh);
    }
}
