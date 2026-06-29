<?php

namespace Leaptel\NBNCo\Objects;

use Leaptel\API\Schemas\SchemaBase;

/**
 * @OA\Schema(description="NBNCo Suggestion from places.nbnco.com.au", type="object")
 * @package Leaptel
 */
class Suggestion extends SchemaBase
{
    /**
     * Location ID (May not be NBN Location id!)
     *
     * @var string
     * @OA\Property()
     */
    public string $id;

    /**
     * Formatted Address
     *
     * @var string
     * @OA\Property()
     */
    public string $formattedAddress;

    /**
     * Latitude
     *
     * @var string
     * @OA\Property()
     */
    public string $latitude;

    /**
     * Longitude
     *
     * @var string
     * @OA\Property()
     */
    public string $longitude;

    /**
     * Source from places response ("lapi", "google", etc). Only "lapi" is valid.
     *
     * @var string
     * @OA\Property()
     */
    public string $source;

    public function isNbnLocation(): bool
    {
        return (strpos($this->id, "LOC") === 0);
    }
}
