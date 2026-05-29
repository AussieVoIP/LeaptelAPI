<?php

namespace Leaptel\API;

use Leaptel\API\Request\SQ;
use Leaptel\API\Response\NBNSQResponse;
use Leaptel\API\Response\WholesalerProduct;
use Leaptel\Models\QueryCache;
use Override;

/** @package Leaptel\API */
class ServiceQual extends APIBase
{
    protected string $path = '/service-qualifications';

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
        if ($loopcount > $this->retrycount) {
            throw new \Exception("Aborting " . $this->getUrl() . " after $loopcount attempts");
        }
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getFullUrl());
        }
        $params = $this->getGuzParams();
        $body = QueryCache::getCachedResult($this->getFullUrl(), $params, 3600);
        if (!$body) {
            $c = $this->getGuzClient();
            $resp = $c->request('POST', $this->getFullUrl(), $params);
            $body = json_decode((string) $resp->getBody(), true);
            QueryCache::cacheResult($this->getFullUrl(), $params, $body);
        }
        $nbn = $body['addresses']['nbn'];
        return new NBNSQResponse($nbn[0]);
    }
}
