<?php

namespace Leaptel\API\Components;

use Leaptel\API\Schemas\SchemaBase;

/**
 * @OA\Schema(description="Raw dump from NBNco NPIS", type="object")
 * @package Leaptel
 */
class NPISServiceQual extends SchemaBase
{

    public function getSupportingProduct(string $portid)
    {

        if (!$this->supportingProduct) {
            return false;
        }
        foreach ($this->supportingProduct as $row) {
            foreach ($row['resourceRef'] as $rr) {
                if (preg_match("/\['$portid'\]/", $rr)) {
                    return $row;
                }
            }
        }
        return false;
    }
}
