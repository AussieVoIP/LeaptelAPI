<?php

namespace Leaptel\API\Service;

use Leaptel\API\APIBase;
use Leaptel\API\Response\ServiceDetails;

/** @package Leaptel\API */
class GetServiceDetails extends APIBase
{
    private string $serviceid;
    protected string $retclass = ServiceDetails::class;

    /**
     * @param string $serviceid
     * @return void
     */
    public function __construct(string $serviceid)
    {
        $this->serviceid = $serviceid;
        $this->path = '/services/' . $this->serviceid;
    }

    /** @return ?ServiceDetails  */
    public function go(bool $refresh = false): ?ServiceDetails
    {
        $validator = function ($body) {
            // If there's no service id, something is messed up
            if (!isset($body['service_id'])) {
                return false;
            }
            // NNI Links don't have avcs
            if (empty($body['avc_id'])) {
                $body['avc_id'] = "OTHER";
            }
            return $body;
        };
        return $this->getSingle($refresh, $validator);
    }
}
