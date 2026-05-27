<?php

namespace Leaptel\API\Customers;

use Leaptel\API\APIBase;

/** @package Leaptel\API */
class DeleteCustomer extends APIBase
{
    protected int $customer_id;

    public function __construct(int $customer_id)
    {
        $this->customer_id = $customer_id;
        $this->path = '/customers/' . $this->customer_id;
    }

    public function go()
    {
        print "Deleting a customer!\n";
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $params['debug'] = true;
        $resp = $c->request('DELETE', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        var_dump($body);
        exit;
    }
}
