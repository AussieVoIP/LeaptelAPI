<?php

namespace Leaptel\API;

use Leaptel\API\Request\CustRequest;
use Leaptel\API\Response\Customer;
use Leaptel\Models\QueryCache;

/** @package Leaptel\API */
class Customers extends APIBase
{
    protected string $path = '/customers';

    /**
     * @param bool $refresh Force refresh
     * @return array<Customer>
     */
    public function go(bool $refresh = false, int $loopcount = 0): array
    {
        $params = $this->getGuzParams();
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getUrl());
        }
        // $params['debug'] = true;
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, 3600);
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getUrl() . "\n";
            }
            $retarr = unserialize($qc['s']);
        } else {
            if ($this->showurl) {
                print $this->getUrl() . "\n";
            }
            $c = $this->getGuzClient();
            $pagination = ["next_page" => 1];
            $retarr = [];
            while ($pagination["next_page"] !== null) {
                $this->query = "?page=" . $pagination["next_page"];
                $resp = $c->request('GET', $this->getFullUrl(), $params);
                $body = json_decode((string) $resp->getBody(), true);
                if (empty($body['customers'])) {
                    $loopcount++;
                    if ($this->showurl) {
                        print "Retrying " . $this->showurl . " - attempt $loopcount\n";
                    }
                    return $this->go(false, $loopcount);
                }
                $pagination = $body['pagination'];
                foreach ($body['customers'] as $row) {
                    $cust = new Customer($row);
                    $retarr[$cust->customer_id] = $cust;
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
