<?php

namespace Leaptel\API;

use Leaptel\API\Response\Customer;
use Leaptel\API\Response\CustomerService;

/** @package Leaptel\API */
class CustServices extends APIBase
{
    public function __construct(string $custid)
    {
        $this->path = '/customers/' . $custid . '/services';
    }

    public function go(int $loopcount = 0)
    {
        if ($loopcount > 5) {
            throw new \Exception("Giving up on " . $this->getFullUrl() . " - too many retries");
        }
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $retarr = [];
        if ($this->showurl) {
            print "CustServices - " . $this->getFullUrl() . "\n";
        }
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        foreach ($body as $row) {
            if (!$row) {
                if ($this->showurl) {
                    print $resp->getBody() . "\n";
                    print "Retrying\n";
                }
                $loopcount++;
                return $this->go($loopcount);
            }
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
