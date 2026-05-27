<?php

namespace Leaptel\API\Response;

use Leaptel\API\Schemas\ResponseBase;

/**
 * @OA\Schema(description="Full Service Response", type="object")
 * @package Leaptel
 */
class ServiceDetails extends CustomerService
{
    /**
     * Username
     *
     * @var string
     * @OA\Property()
     */
    public string $ppoe_username;

    /**
     * Password
     *
     * @var string
     * @OA\Property()
     */
    public string $ppoe_password;
}
