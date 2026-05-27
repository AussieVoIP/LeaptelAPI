<?php

namespace Leaptel\API;

use Leaptel\API\Request\CustRequest;
use Leaptel\API\Response\Customer;
use Leaptel\Models\QueryCache;

/** @package Leaptel\API */
class Customers extends APIBase
{
    protected string $path = '/customers';

    /** @return array<\Leaptel\API\Response\Customer>  */
    public function go(): array
    {
        $params = $this->getGuzParams();
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, 3600);
        if ($qc) {
            $retarr = unserialize($qc['s']);
        } else {
            $c = $this->getGuzClient();
            $pagination = ["next_page" => 1];
            $retarr = [];
            while ($pagination["next_page"] !== null) {
                $this->query = "?page=" . $pagination["next_page"];
                $resp = $c->request('GET', $this->getFullUrl(), $params);
                $body = json_decode((string) $resp->getBody(), true);
                $pagination = $body['pagination'];
                foreach ($body['customers'] as $row) {
                    $c = new Customer($row);
                    $retarr[$c->customer_id] = $c;
                }
            }
            QueryCache::cacheResult($this->getUrl(), $params, ["s" => serialize($retarr)]);
        }
        foreach ($this->filters as $callable) {
            foreach ($retarr as $k => $v) {
                $r = $callable($v);
                if ($r === false) {
                    unset($retarr[$k]);
                }
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
