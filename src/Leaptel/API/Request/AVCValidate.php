<?php

namespace Leaptel\API\Request;

use Leaptel\API\Schemas\RequestBase;

/**
 * @OA\Schema(description="Validate AVC at Location", type="object")
 * @package Leaptel
 */
class AVCValidate extends RequestBase
{
    // Sadly, there isn't a ntd_port param available from nbnco. So this returns all
    // ports with a match flag, if you've given it the correct avc.

    /**
     * Location ID
     *
     * @var string
     * @OA\Property()
     */
    public string $location_id;

    /**
     * AVC ID
     *
     * @var string
     * @OA\Property()
     */
    public string $avc_id;
}
