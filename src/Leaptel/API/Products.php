<?php

namespace Leaptel\API;

use Leaptel\API\Response\WholesalerProduct;
use Leaptel\Models\QueryCache;

/** @package Leaptel\API */
class Products extends APIBase
{
    protected string $path = '/products';
    private array $validcarriers = ["nbn" => true, "opt" => true, "ltn" => true, "red" => true, "asn" => true, "ftr" => true];

    public function __construct(string $carrier = 'nbn')
    {
        if (empty($this->validcarriers[$carrier])) {
            throw new \Exception("Invalid carrier $carrier");
        }
        $this->query = "?carrier=$carrier";
    }

    /** @return array<\Leaptel\API\Response\WholesalerProduct>  */
    public function go(bool $refresh = false, int $loopcount = 0): array
    {
        if ($loopcount > $this->retrycount) {
            throw new \Exception("Aborting " . $this->getUrl() . " after $loopcount attempts");
        }
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getFullUrl());
        }
        $q = QueryCache::getCachedResult($this->getFullUrl(), [], 86400);
        if (!$q) {
            $c = $this->getGuzClient();
            $params = $this->getGuzParams();
            $resp = $c->request('GET', $this->getFullUrl(), $params);
            $q = json_decode((string) $resp->getBody(), true);
            // Make sure there are at least 5 results returned
            if (count($q) <= 5) {
                $loopcount++;
                return $this->go(false, $loopcount);
            }
            QueryCache::cacheResult($this->getFullUrl(), [], $q);
        }
        $resp = [];
        foreach ($q as $row) {
            $p = new WholesalerProduct($row);
            foreach ($this->filters as $callable) {
                $r = $callable($p);
                if ($r === false) {
                    break (2);
                }
            }
            $resp[$p->product_id] = $p;
        }
        return $resp;
    }
}
