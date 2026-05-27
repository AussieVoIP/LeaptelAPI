<?php

namespace Leaptel\API\Request;

use Leaptel\API\Schemas\RequestBase;
use Leaptel\API\Traits\CustomerTrait;

/**
 * @OA\Schema(description="Customer Request", type="object")
 * @package Leaptel
 */
class CustRequest extends RequestBase
{
    use CustomerTrait;
}
