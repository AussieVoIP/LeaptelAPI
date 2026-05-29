<?php

namespace Leaptel\API\Components;

use Leaptel\API\Schemas\SchemaBase;
use Leaptel\API\Traits\ContactTrait;

/**
 * @OA\Schema(description="Contact for NBN Order", type="object")
 * @package Leaptel
 */
class OrderContact extends SchemaBase
{
    use ContactTrait;
}
