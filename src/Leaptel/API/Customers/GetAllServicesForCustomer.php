<?php

namespace Leaptel\API\Customers;

use Leaptel\API\APIBase;
use Leaptel\API\Response\CustomerService;

/** @package Leaptel\API */
class GetAllServicesForCustomer extends APIBase
{
    protected string $retclass = CustomerService::class;
    protected string $indexby = "service_id";

    public function __construct(string $custid)
    {
        $this->path = '/customers/' . $custid . '/services';
    }

    /**
     * @param bool $refresh
     * @return CustomerService[]
     */
    public function go(bool $refresh = false)
    {
        return $this->getMultipleNotPaginated($refresh);
    }
}
