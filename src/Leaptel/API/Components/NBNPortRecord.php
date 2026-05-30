<?php

namespace Leaptel\API\Components;

use Leaptel\API\Response\NBNSQResponse;
use Leaptel\API\Schemas\SchemaBase;
use Leaptel\Models\NBNService;
use Override;

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

    /**
     * Location ID
     *
     * @var string
     * @OA\Property()
     */
    public string $location_id = "";

    public ?array $PortDetails = null;

    public mixed $supportingProduct = null;

    public function getPortDetailsString(): string
    {
        if (!$this->PortDetails) {
            return "";
        }
        $spname = $this->PortDetails['serviceProviderName'] ?? "Unknown";
        $spid = $this->PortDetails['serviceProviderId'] ?? "9999";
        return " $spname ($spid)";
    }

    public function addNPIS(NPISServiceQual $npis)
    {
        $port = $npis->getSupportingProduct($this->PortName);
        if ($port) {
            $this->PortDetails = $port;
        }
    }

    public function isRelatedPort(array $custids)
    {
        if (!$this->PortDetails) {
            return "create";
        }
        $spid = $this->PortDetails['serviceProviderId'] ?? "9999";
        if ($spid != "0662") {
            return "port";
        }
        // Now see if this port is owned by a custid we know
        $service = NBNService::where(['location_id' => $this->location_id, 'portnum' => $this->PortNumber])->get();
        foreach ($service as $s) {
            if (!empty($custids[$s->customer_id])) {
                return $s->service_id;
            }
        }
        return "Error";
    }

    public function getDescription(bool $withstatus = true): string
    {
        if ($withstatus) {
            return $this->Status . " " . $this->getPortDetailsString();
        }
        return $this->getPortDetailsString();
    }
}
