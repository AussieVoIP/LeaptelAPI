<?php

namespace Leaptel\API\Traits;

/**
 * @OA\Schema(description="Contact Trait (for Orders)", type="object")
 * @package Leaptel
 */
trait ContactTrait
{
    /**
     * First Name
     *
     * @var string
     * @OA\Property()
     */
    public string $contact_first_name;

    /**
     * Last Name
     *
     * @var string
     * @OA\Property()
     */
    public string $contact_last_name;

    /**
     * Phone
     *
     * @var string
     * @OA\Property()
     */
    public string $contact_phone = "";

    /**
     * Email
     *
     * @var string
     * @OA\Property()
     */
    public string $contact_email = "";

    /**
     * Address
     *
     * @var string
     * @OA\Property()
     */
    public string $contact_address;

    /**
     * Suburb
     *
     * @var string
     * @OA\Property()
     */
    public string $contact_suburb;

    /**
     * State
     *
     * @var string
     * @OA\Property()
     */
    public string $contact_state;

    /**
     * Postcode
     *
     * @var string
     * @OA\Property()
     */
    public string $contact_postcode;
}
