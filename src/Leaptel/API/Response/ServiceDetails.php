<?php

namespace Leaptel\API\Response;

/**
 * @OA\Schema(description="Full Service Response", type="object")
 * @package Leaptel
 */
class ServiceDetails extends CustomerService
{
    /**
     * Username
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $ppoe_username;

    /**
     * Password
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $ppoe_password;
}
