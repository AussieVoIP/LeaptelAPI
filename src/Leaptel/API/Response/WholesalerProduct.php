<?php

namespace Leaptel\API\Response;

use Leaptel\API\Schemas\ResponseBase;

/**
 * @OA\Schema(description="Product Information", type="object")
 * @package Leaptel
 */
class WholesalerProduct extends ResponseBase
{
    /**
     * Product ID
     *
     * @var string
     * @OA\Property()
     */
    public string $product_id;

    /**
     * Plan ID
     *
     * @var int
     * @OA\Property()
     */
    public int $plan_id;

    /**
     * Plan Name
     *
     * @var string
     * @OA\Property()
     */
    public string $plan_name;

    /**
     * Monthly Cost (String, use bc* functions)
     *
     * @var string
     * @OA\Property()
     */
    public string $monthly_cost;

    /**
     * Access Types
     *
     * @var array
     * @OA\Property()
     */
    public array $access_type = [];

    /**
     * Handoff Type
     *
     * @var string
     * @OA\Property()
     */
    public string $handoff_type;

    /**
     * SLA Levels
     *
     * @var array
     * @OA\Property()
     */
    public array $elsa;
}
