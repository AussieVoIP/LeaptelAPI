<?php

namespace Leaptel\API\Customers;

use Leaptel\API\APIBase;
use Leaptel\API\Response\CustomerOrder;
use Leaptel\Models\QueryCache;

/** @package Leaptel\API */
class CustomerOrders extends APIBase
{
    protected int $customer_id;
    // protected bool $showurl = true;

    public function __construct(int $customer_id)
    {
        $this->customer_id = $customer_id;
        $this->path = '/customers/' . $this->customer_id . "/orders";
    }

    /** @return \Leaptel\API\Response\CustomerOrder[]  */
    public function go(bool $refresh = false, int $loopcount = 0)
    {
        if ($loopcount > 5) {
            throw new \Exception("Giving up on " . $this->getFullUrl() . " - too many retries");
        }
        $params = $this->getGuzParams();
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getUrl());
        }
        $qc = QueryCache::getCachedResult($this->getFullUrl(), $params, 3600);
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getFullUrl() . "\n";
            }
            $retarr = unserialize($qc['s']);
        } else {
            if ($this->showurl) {
                print $this->getUrl() . "\n";
            }
            $c = $this->getGuzClient();
            $resp = $c->request('GET', $this->getFullUrl(), $params);
            $body = json_decode((string) $resp->getBody(), true);
            $retarr = [];
            foreach ($body as $o) {
                if (empty($o['order_id'])) {
                    $loopcount++;
                    if ($this->showurl) {
                        print "Retrying " . $this->showurl . " - attempt $loopcount\n";
                        print $resp->getBody() . "\n";
                    }
                    return $this->go(false, $loopcount);
                }
                $o['timestamp'] = time();
                $order = new CustomerOrder($o);
                $retarr[$order->order_id] = $order;
            }
            QueryCache::cacheResult($this->getUrl(), $params, ["s" => serialize($retarr)]);
        }
        return $retarr;
    }
}
