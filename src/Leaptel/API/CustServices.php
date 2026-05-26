<?php

namespace Leaptel\API;

use Leaptel\API\Response\Customer;
use Leaptel\API\Response\CustomerService;

/** @package Leaptel\API */
class CustServices extends APIBase
{
    private Customer $c;

    public function __construct(Customer $c)
    {
        $this->c = $c;
        $this->path = '/customers/' . $c->customer_id . '/services';
    }

    public function go()
    {
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $retarr = [];
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        foreach ($body as $row) {
            $p = new CustomerService($row);
            foreach ($this->filters as $callable) {
                $r = $callable($p);
                if ($r === false) {
                    break (2);
                }
            }
            $retarr[$p->service_id] = $p;
        }
        return $retarr;
    }
}
