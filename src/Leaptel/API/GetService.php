<?php

namespace Leaptel\API;

use Leaptel\API\Response\ServiceDetails;

/** @package Leaptel\API */
class GetService extends APIBase
{
    private string $serviceid;

    /**
     * @param string $serviceid
     * @return void
     */
    public function __construct(string $serviceid)
    {
        $this->serviceid = $serviceid;
        $this->path = '/services/' . $this->serviceid;
    }

    /** @return ServiceDetails  */
    public function go(): ServiceDetails
    {
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        return new ServiceDetails($body);
    }
}
