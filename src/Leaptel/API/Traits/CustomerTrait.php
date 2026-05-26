<?php

namespace Leaptel\API\Traits;

/**
 * @OA\Schema(description="Customer Information Trait", type="object")
 * @package Leaptel
 */
trait CustomerTrait
{
    /**
     * First Name
     *
     * @var string
     * @OA\Property()
     */
    public string $first_name;

    /**
     * Last Name
     *
     * @var string
     * @OA\Property()
     */
    public string $last_name;

    /**
     * Birthdate
     *
     * @var string
     * @OA\Property()
     */
    public string $birthdate;

    /**
     * Email
     *
     * @var string
     * @OA\Property()
     */
    public string $email;

    /**
     * Mobile
     *
     * @var string
     * @OA\Property()
     */
    public string $mobile;

    /**
     * Phone
     *
     * @var string
     * @OA\Property()
     */
    public string $phone = "";

    /**
     * Fax
     *
     * @var string
     * @OA\Property()
     */
    public string $fax = "";

    /**
     * Address 1
     *
     * @var string
     * @OA\Property()
     */
    public string $address1;

    /**
     * Address 2
     *
     * @var string
     * @OA\Property()
     */
    public string $address2;

    /**
     * City
     *
     * @var string
     * @OA\Property()
     */
    public string $city;

    /**
     * State
     *
     * @var string
     * @OA\Property()
     */
    public string $state;

    /**
     * Postcode
     *
     * @var string
     * @OA\Property()
     */
    public string $postcode;

    /**
     * Postal Address
     *
     * @var string
     * @OA\Property()
     */
    public string $postal_address = "";

    /**
     * Postal City
     *
     * @var string
     * @OA\Property()
     */
    public string $postal_city = "";

    /**
     * Postal State
     *
     * @var string
     * @OA\Property()
     */
    public string $postal_state = "";

    /**
     * Postal Postcode
     *
     * @var string
     * @OA\Property()
     */
    public string $postal_postcode = "";
}
