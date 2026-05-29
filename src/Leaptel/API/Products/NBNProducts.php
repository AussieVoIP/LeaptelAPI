<?php

namespace Leaptel\API\Products;

use Leaptel\API\APIBase;
use Leaptel\API\Response\WholesalerProduct;
use Leaptel\Models\QueryCache;

/** @package Leaptel\API */
class NBNProducts extends APIBase
{
    protected string $path = '/products';
    protected string $query = '?carrier=nbn';

    protected string $retclass = WholesalerProduct::class;
    protected string $indexby = "plan_id";
    protected int $cacheforsecs = 86400;


    /** @return array<\Leaptel\API\Response\WholesalerProduct>  */
    public function go(bool $refresh = false): array
    {
        return $this->getMultipleNotPaginated($refresh);
    }
}
