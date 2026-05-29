<?php

namespace Leaptel\API\Customers;

use Leaptel\API\APIBase;
use Leaptel\API\Request\CustRequest;
use Leaptel\API\Response\Customer;

/** @package Leaptel\API */
class GetAllCustomers extends APIBase
{
    protected string $path = '/customers';

    protected string $retclass = Customer::class;
    protected string $indexby = "customer_id";
    protected int $cacheforsecs = 86400;

    /**
     * @param bool $refresh Force refresh
     * @return array<Customer>
     */
    public function go(bool $refresh = false)
    {
        return $this->getMultiplePaginated("customers", "pagination", $refresh);
    }

    public function createCustomer(CustRequest $c)
    {
        print "Creating a customer!\n";
        exit;
    }
}
