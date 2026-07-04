<?php

namespace Leaptel\API\Response;

use Leaptel\API\Schemas\ResponseBase;

/**
 * @OA\Schema(description="A usable Service Assurance Test", type="object")
 * @package Leaptel
 */
class ServiceAssuranceTest extends ResponseBase
{
    /**
     * Test Name
     *
     * @var string
     * @OA\Property()
     */
    public string $test_name;

    /**
     * Test Abbreviation
     *
     * @var string
     * @OA\Property()
     */
    public string $abbreviation;

    /**
     * Service Impacting
     *
     * @var int
     * @OA\Property()
     */
    public int $service_impacting;

    /**
     * Test Behaviour
     *
     * @var string
     * @OA\Property()
     */
    public string $behaviour;

    /**
     * Test Code
     *
     * @var string
     * @OA\Property()
     */
    public string $test_code;

    /**
     * Test Description
     *
     * @var string
     * @OA\Property()
     */
    public string $description;

    /**
     * Technology Type (Added by GetServiceAssuranceTest)
     *
     * @var string
     * @OA\Property()
     */
    public string $techtype;
}
