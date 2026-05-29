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
    public function go(int $loopcount = 0): ServiceDetails
    {
        if ($loopcount > 5) {
            throw new \Exception("Giving up on " . $this->getFullUrl() . " - too many retries");
        }
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        if ($this->showurl) {
            print "GetService - " . $this->getFullUrl() . "\n";
        }
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        if (!empty($body['message'])) {
            if ($this->showurl) {
                print $resp->getBody() . "\n";
                print "Retrying\n";
            }
            $loopcount++;
            return $this->go($loopcount);
        }
        if (empty($body['avc_id'])) {
            $body['avc_id'] = "OTHER";
        }
        return new ServiceDetails($body);
    }
}
