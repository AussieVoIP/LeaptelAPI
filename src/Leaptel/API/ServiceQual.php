<?php

namespace Leaptel\API;

use Leaptel\API\Request\SQ;
use Leaptel\API\Response\NBNSQResponse;
use Leaptel\API\Response\WholesalerProduct;
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

    /** @return array<\Leaptel\API\Response\WholesalerProduct>  */
    public function go(): array
    {
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $resp = $c->request('POST', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        $nbn = $body['addresses']['nbn'];
        $sqresp = new NBNSQResponse($nbn[0]);
        var_dump($sqresp);
        exit;
        $resp = [];
        foreach ($body as $row) {
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
