<?php

namespace Leaptel\API\Response;

use Leaptel\API\Schemas\ResponseBase;
use Leaptel\API\Traits\CustomerTrait;

/**
 * @OA\Schema(description="Customer Information", type="object")
 * @package Leaptel
 */
class CustomerResponse extends ResponseBase
{
    use CustomerTrait;

    /**
     * Customer ID
     *
     * @var string
     * @OA\Property()
     */
    public string $customer_id;

    /**
     * Company Name
     *
     * @var string
     * @OA\Property()
     */
    public string $company_name;

    /**
     * Address1
     *
     * @var string
     * @OA\Property()
     */
    public string $address1;

    /**
     * Address2
     *
     * @var string
     * @OA\Property()
     */
    public string $address2;

    /**
     * Postal Address 1
     *
     * @var string
     * @OA\Property()
     */
    public string $postal_address1;

    /**
     * Postal Address 2
     *
     * @var string
     * @OA\Property()
     */
    public string $postal_address2;

    /**
     * Active ("yes" or "no")
     *
     * @var string
     * @OA\Property()
     */
    public string $active;

    /**
     * Billing Day
     *
     * @var string
     * @OA\Property()
     */
    public string $billing_day;
}
