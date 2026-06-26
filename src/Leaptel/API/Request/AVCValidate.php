<?php

namespace Leaptel\API\Request;

use Leaptel\API\Schemas\RequestBase;

/**
 * @OA\Schema(description="Validate AVC at Location", type="object")
 * @package Leaptel
 */
class AVCValidate extends RequestBase
{
    // This API call REALLY needs a ntd_port param, because wow it's slow. I'm guessing
    // it iterates over every port in the backend. It took 6 seconds to run the first time
    // over a location.

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
