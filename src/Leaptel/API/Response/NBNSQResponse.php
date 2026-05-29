<?php

namespace Leaptel\API\Response;

use Leaptel\API\Components\NBNPortRecord;
use Leaptel\API\Schemas\ResponseBase;
use Leaptel\API\Traits\ServiceQualTrait;

/**
 * @OA\Schema(description="NBN Service Qualification Response", type="object")
 * @package Leaptel
 */
class NBNSQResponse extends ResponseBase
{
    use ServiceQualTrait;

    public array $__mappings = [
        "lot_no" => "lotNumber",
        "street_number" => "roadNumber1",
        "street_name" => ["roadName", "roadTypeCode"],
        "suburb" => "localityName",
        "state" => "stateTerritoryCode",
    ];

    protected function finishImport(array $row)
    {
        $records = $row['NBNPortRecord'] ?? [];
        $this->ntd_ports = [];
        foreach ($records as $r) {
            $pr = new NBNPortRecord($r);
            $this->ntd_ports[$pr->Id] = $pr;
        }
    }

    /**
     * ID (Always Location ID?)
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
     * NBN Technology Type
     *
     * @var string
     * @OA\Property()
     */
    public string $access_technology;

    /**
     * Serviceable
     *
     * @var boolean
     * @OA\Property()
     */
    public bool $serviceable;

    /**
     * Service Class Description
     *
     * @var string
     * @OA\Property()
     */
    public string $service_class_description;

    /**
     * Service Type
     *
     * @var string
     * @OA\Property()
     */
    public string $service_type;

    /**
     * Service Type Description
     *
     * @var string
     * @OA\Property()
     */
    public string $service_type_description;

    /**
     * Speed Tiers
     *
     * @var array
     * @OA\Property()
     */
    public array $speed_tiers_array;

    /**
     * Max Download Speed
     *
     * @var int
     * @OA\Property()
     */
    public int $max_download_speed;

    /**
     * Max Upload Speed
     *
     * @var int
     * @OA\Property()
     */
    public int $max_upload_speed;

    /**
     * Speed Tier Notes
     *
     * @var string
     * @OA\Property()
     */
    public string $speed_tier_notes;

    /**
     * Equipment Type
     *
     * @var string
     * @OA\Property()
     */
    public string $equipment_type;

    /**
     * Equipment Version
     *
     * @var string
     * @OA\Property()
     */
    public string $equipment_version;

    /**
     * NTD Location ('indoor' usually)
     *
     * @var string
     * @OA\Property()
     */
    public string $ntd_location;

    /**
     * NTD Type
     *
     * @var string
     * @OA\Property()
     */
    public string $ntd_type;

    /**
     * Bandwidth Upstream
     *
     * @var int
     * @OA\Property()
     */
    public int $ntd_bandwidth_upstream;

    /**
     * Bandwidth Downstream
     *
     * @var int
     * @OA\Property()
     */
    public int $ntd_bandwidth_downstream;

    /**
     * Access Type
     *
     * @var string
     * @OA\Property()
     */
    public string $access_type;

    /**
     * NTD Ports
     *
     * @var array
     * @OA\Property()
     */
    public array $ntd_ports;
}
