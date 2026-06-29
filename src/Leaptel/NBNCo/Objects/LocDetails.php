<?php

namespace Leaptel\NBNCo\Objects;

use Leaptel\API\Schemas\SchemaBase;
use Leaptel\Models\PlacesDetail;

/**
 * This is only used to get the PlacesDetail object currently.
 *
 * @OA\Schema(description="NBNCo Location details from places.nbnco.com.au", type="object")
 * @package Leaptel
 */
class LocDetails extends SchemaBase
{
    public static function fromDetailsResponse(array $src): LocDetails
    {
        $m = new static();
        $ad = $src['addressDetail'];
        $sa = $src['servingArea'];
        $m->location_id = $ad['id'];
        $admaps = [
            "latitude",
            "longitude",
            "formattedAddress",
            "address1",
            "address2",
            "locality",
            "techType",
            "serviceType",
            "serviceStatus",
        ];
        foreach ($admaps as $k) {
            $m->{$k} = $ad[$k];
        }
        $m->csaId = $sa['csaId'];
        $m->area_description = $sa['description'];
        return $m;
    }

    public static function getPlacesDetail(array $src)
    {
        $ld = static::fromDetailsResponse($src);
        $key = ["location_id" => $ld->location_id];
        $params = $ld->toArray();
        $params['raw'] = $src;
        $pd = PlacesDetail::firstOrCreate($key, $params);
        return $pd;
    }

    /**
     * Location ID
     *
     * @var string
     * @OA\Property()
     */
    public string $location_id;

    /**
     * Formatted Address
     * ("DWELLING LOT 20 1 GRAYSON ST WEST GLADSTONE QLD 4680 AUSTRALIA")
     *
     * @var string
     * @OA\Property()
     */
    public string $formattedAddress;

    /**
     * Address1
     * ("Dwelling Lot 20 1 Grayson St")
     *
     * @var string
     * @OA\Property()
     */
    public string $address1;

    /**
     * Address2
     * ("West Gladstone QLD 4680 Australia")
     *
     * @var string
     * @OA\Property()
     */
    public string $address2;

    /**
     * Locality
     * ("West Gladstone")
     *
     * @var string
     * @OA\Property()
     */
    public string $locality;

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
     * CSA ID
     *
     * @var string
     * @OA\Property()
     */
    public string $csaId;

    /**
     * Service Type
    /**
     * Tech Type
     *
     * @var string
     * @OA\Property()
     */
    public string $techType;

    /**
     * Service Type
     *
     * @var string
     * @OA\Property()
     */
    public string $serviceType;

    /**
     * Service Status
     *
     * @var string
     * @OA\Property()
     */
    public string $serviceStatus;

    /**
     * Area Description (Is serviceArea.description in response)
     *
     * @var string
     * @OA\Property()
     */
    public string $area_description;
}
