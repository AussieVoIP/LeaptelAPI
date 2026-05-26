<?php

namespace Leaptel\API\Request;

use Leaptel\API\Schemas\RequestBase;
use Leaptel\API\Traits\ServiceQualTrait;

/**
 * @OA\Schema(description="Service Qualification Request", type="object")
 * @package Leaptel
 */
class SQ extends RequestBase
{
    use ServiceQualTrait;
}
