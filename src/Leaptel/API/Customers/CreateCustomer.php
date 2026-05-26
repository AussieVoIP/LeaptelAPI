<?php

namespace Leaptel\API\Customers;

use Leaptel\API\APIBase;
use Leaptel\API\Request\CustRequest;
use Override;

/** @package Leaptel\API */
class CreateCustomer extends APIBase
{
    protected string $path = '/customers';
    protected CustRequest $cr;

    public function __construct(CustRequest $cr)
    {
        $this->cr = $cr;
    }

    #[Override]
    public function getFormParams()
    {
        return $this->cr->toArray();
    }

    public function go()
    {
        print "Creating a customer!\n";
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $resp = $c->request('POST', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        var_dump($body);
        exit;
    }
}
