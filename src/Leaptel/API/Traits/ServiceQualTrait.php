<?php

namespace Leaptel\API\Traits;

/**
 * @OA\Schema(description="Service Qualification Trait", type="object")
 * @package Leaptel
 */
trait ServiceQualTrait
{

    /**
     * Location ID - If provided as part of a request, everything else is ignored
     *
     * @var string
     * @OA\Property()
     */
    public string $location_id = "";

    /**
     * AVC ID
     *
     * @var string
     * @OA\Property()
     */
    public string $avc_id = "";

    /**
     * Lot Number
     *
     * @var integer
     * @OA\Property()
     */
    public ?int $lot_no = null;

    /**
     * Unit
     *
     * @var string
     * @OA\Property()
     */
    public string $unit = "";

    /**
     * Level
     *
     * @var string
     * @OA\Property()
     */
    public string $level = "";

    /**
     * Street Number
     *
     * @var integer
     * @OA\Property()
     */
    public ?int $street_number = null;

    /**
     * Street Name
     *
     * @var string
     * @OA\Property()
     */
    public string $street_name = "";

    /**
     * Suburb
     *
     * @var string
     * @OA\Property()
     */
    public string $suburb = "";

    /**
     * State
     *
     * @var string
     * @OA\Property()
     */
    public string $state = "";

    /**
     * Postcode
     *
     * @var string
     * @OA\Property()
     */
    public string $postcode = "";

    /**
     * Phone Service
     *
     * @var string
     * @OA\Property()
     */
    public string $phone_service = "";

    /**
     * Fibre On Demand
     *
     * @var string
     * @OA\Property()
     */
    public string $fibreOnDemand = "";
}
