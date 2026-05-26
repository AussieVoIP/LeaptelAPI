<?php

namespace Leaptel\API;

use Leaptel\API\Response\WholesalerProduct;

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
    public function go(): array
    {
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
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
