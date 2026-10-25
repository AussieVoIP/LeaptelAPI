<?php

namespace Leaptel\API\Customers;

use Leaptel\API\APIBase;
use Leaptel\API\Request\CustRequest;
use Leaptel\API\Response\CustomerResponse;
use Leaptel\Models\Customer;

/** @package Leaptel\API */
class GetAllCustomers extends APIBase
{
    protected string $path = '/customers';

    protected string $retclass = CustomerResponse::class;
    protected string $indexby = "customer_id";
    protected int $cacheforsecs = 86400;

    /**
     * @param bool $refresh Force refresh
     * @return array<CustomerResponse>
     */
    public function go(bool $refresh = false)
    {
        // Customers we already hold locally, so any the API no longer returns can be removed
        $current = Customer::get()->keyBy('id')->all();

        // Ask the API for them
        $custs = $this->getMultiplePaginated("customers", "pagination", $refresh);

        $retarr = [];
        foreach ($custs as $id => $cr) {
            $retarr[$id] = Customer::fromCustomerResponse($cr);
            unset($current[$id]);
        }
        // Anything left in $current is no longer known to the API
        foreach ($current as $old) {
            $old->delete();
        }
        return $retarr;
    }

    public function createCustomer(CustRequest $c)
    {
        print "Creating a customer!\n";
        exit;
    }
}
