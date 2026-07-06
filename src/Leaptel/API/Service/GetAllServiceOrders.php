<?php

namespace Leaptel\API\Service;

use Leaptel\API\APIBase;
use Leaptel\API\Response\CustomerOrder;

/** @package Leaptel\API */
class GetAllServiceOrders extends APIBase
{
    protected string $retclass = CustomerOrder::class;
    protected string $indexby = "order_id";
    protected int $cacheforsecs = 86400;

    // Generate a hash of the raw result
    protected bool $addhash = true;


    /**
     * @param string $serviceid
     * @return void
     */
    public function __construct(
        private string $serviceid
    ) {
        $this->path = '/services/' . $this->serviceid . '/orders';
    }

    /** @return \Leaptel\API\Response\CustomerOrder[]  */
    public function go(bool $refresh = false): array
    {
        return $this->getMultipleNotPaginated($refresh);
    }
}
