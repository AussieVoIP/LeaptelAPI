<?php

namespace Leaptel\Addressify\Objects;

use Leaptel\API\Schemas\SchemaBase;

/**
 * @OA\Schema(description="AddressInfo Object from Addressify", type="object")
 * @package Leaptel
 */
class AddressInfo extends SchemaBase
{
    protected string $__raw = "";

    /**
     * Address Full Description
     *
     * @var string
     * @OA\Property()
     */
    public string $AddressFull;

    /**
     * Source of address lookup
     *
     * @var string
     * @OA\Property()
     */
    public string $Source = "Addressify";

    /**
     * Valid
     *
     * @var bool
     * @OA\Property()
     */
    public bool $Valid;

    /**
     * Address Line 1
     *
     * @var string
     * @OA\Property()
     */
    public string $AddressLine1 = "";

    /**
     * Address Line 2
     *
     * @var string
     * @OA\Property()
     */
    public string $AddressLine2 = "";

    /**
     * Suburb
     *
     * @var string
     * @OA\Property()
     */
    public string $Suburb;

    /**
     * Postcode
     *
     * @var string
     * @OA\Property()
     */
    public string $Postcode;

    /**
     * Number
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $Number;

    /**
     * Number First
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $NumberFirst;

    /**
     * Number Last
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $NumberLast;

    /**
     * Street Suffix Full
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $StreetSuffixFull;

    /**
     * State
     *
     * @var string
     * @OA\Property()
     */
    public string $State;

    /**
     * Street
     *
     * @var string
     * @OA\Property()
     */
    public string $Street;

    /**
     * Street Type
     *
     * @var string
     * @OA\Property()
     */
    public string $StreetType;

    /**
     * Street Type Full
     *
     * @var string
     * @OA\Property()
     */
    public string $StreetTypeFull;

    /**
     * Street Line
     *
     * @var string
     * @OA\Property()
     */
    public string $StreetLine;

    /**
     * BuildingName
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $BuildingName;

    /**
     * Unit Type
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $UnitType;

    /**
     * Unit Type Full
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $UnitTypeFull;

    /**
     * Unit Number
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $UnitNumber;

    /**
     * Level Type
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $LevelType;

    /**
     * Level Type Full
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $LevelTypeFull;

    /**
     * Level Number
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $LevelNumber;
}
