<?php

namespace Leaptel\API\Location;

use Leaptel\API\APIBase;
use Leaptel\API\Request\SQ;
use Leaptel\API\Response\NBNSQResponse;
use Leaptel\Models\QueryCache;
use Override;

/** @package Leaptel\API */
class GetServiceQual extends APIBase
{
    protected string $path = '/service-qualifications';

    protected string $retclass = NBNSQResponse::class;
    protected int $cacheforsecs = 86400;
    protected string $addtimestamp = "timestamp";

    protected SQ $sq;

    public function __construct(SQ $sq)
    {
        $this->sq = $sq;
    }

    #[Override]
    public function getFormParams()
    {
        return $this->sq->toArray();
    }

    /**
     * @param bool $refresh
     * @return NBNSQResponse
     */
    public function go(bool $refresh = false, int $loopcount = 0): NBNSQResponse
    {
        $validator = function ($body) {
            if (empty($body['addresses'])) {
                return false;
            }
            $bigbody = $body['addresses']['nbn'][0];
            unset($body['addresses']['nbn'][0]);
            $bodybody['__other'] = $body;
            $defaults = [
                "lotNumber" => null,
            ];
            foreach ($defaults as $k => $v) {
                if (empty($bigbody[$k])) {
                    $bigbody[$k] = $v;
                }
            }
            return $bigbody;
        };
        return $this->postSingle($refresh, $validator);
    }
}
