<?php

namespace Leaptel\API;

use Leaptel\API\Request\CustRequest;
use Leaptel\API\Response\Customer;

/** @package Leaptel\API */
class Customers extends APIBase
{
    protected string $path = '/customers';

    /** @return array<\Leaptel\API\Response\Customer>  */
    public function go(): array
    {
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $pagination = ["next_page" => 1];
        $retarr = [];
        while ($pagination["next_page"] !== null) {
            $this->query = "?page=" . $pagination["next_page"];
            $resp = $c->request('GET', $this->getFullUrl(), $params);
            $body = json_decode((string) $resp->getBody(), true);
            $pagination = $body['pagination'];
            foreach ($body['customers'] as $row) {
                $c = new Customer($row);
                foreach ($this->filters as $callable) {
                    $r = $callable($c);
                    if ($r === false) {
                        break (2);
                    }
                }
                $retarr[$c->customer_id] = $c;
            }
        }
        return $retarr;
    }

    public function createCustomer(CustRequest $c)
    {
        print "Creating a customer!\n";
        exit;
    }
}
