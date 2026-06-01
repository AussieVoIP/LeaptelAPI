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
        // Get all current customers in the db, to see if they need to be cleaned up
        $tmparr = Customer::get();
        $current = [];
        foreach ($tmparr as $c) {
            if ($refresh) {
                $c->delete();
            } else {
                $current[$c->id] = $c;
            }
        }

        // Ask the API for them
        $custs = $this->getMultiplePaginated("customers", "pagination", $refresh);

        $retarr = [];
        foreach ($custs as $id => $cr) {
            if (empty($current[$id])) {
                $c = Customer::fromCustomerResponse($cr);
            } else {
                $c = $current[$id];
            }
            $retarr[$id] = $c;
            unset($current[$id]);
        }
        // Anything left in $current is old and bad, and should be deleted
        foreach ($current as $old) {
            print "I want to delete " . json_encode($old) . "\n";
            exit;
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
