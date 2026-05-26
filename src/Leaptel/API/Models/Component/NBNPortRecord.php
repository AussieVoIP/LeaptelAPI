<?php

namespace Leaptel\API\Models\Component;

use Leaptel\API\Schemas\SchemaBase;

/**
 * @OA\Schema(description="NBN Port Record, from Service Qualification", type="object")
 * @package Leaptel
 */
class NBNPortRecord extends SchemaBase
{
    /**
     * NTD ID
     *
     * @var string
     * @OA\Property()
     */
    public string $NTDID;

    /**
     * Port Number
     *
     * @var string
     * @OA\Property()
     */
    public string $PortNumber;

    /**
     * Port Name
     *
     * @var string
     * @OA\Property()
     */
    public string $PortName;

    /**
     * Port ID (Same as portname?)
     *
     * @var string
     * @OA\Property()
     */
    public string $Id;

    /**
     * Available
     *
     * @var int
     * @OA\Property()
     */
    public int $Available;

    /**
     * Port Type
     *
     * @var string
     * @OA\Property()
     */
    public string $PortType;

    /**
     * Churn
     *
     * @var int
     * @OA\Property()
     */
    public int $Churn;

    /**
     * Equipment Type
     *
     * @var string
     * @OA\Property()
     */
    public string $EquipmentType;

    /**
     * Equipment Version
     *
     * @var string
     * @OA\Property()
     */
    public string $EquipmentVersion;

    /**
     * Status
     *
     * @var string
     * @OA\Property()
     */
    public string $Status;

    /**
     * New Line Charge
     *
     * @var int
     * @OA\Property()
     */
    public int $NewLineCharge;

    /**
     * Self Install
     *
     * @var int
     * @OA\Property()
     */
    public int $SelfInstall;
}
