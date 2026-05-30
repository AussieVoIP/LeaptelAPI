<?php

namespace Leaptel\API\Components;

use Leaptel\API\Response\NBNSQResponse;
use Leaptel\API\Schemas\SchemaBase;
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

    public ?array $PortDetails = null;

    public function getPortDetailsString(): string
    {
        if (!$this->PortDetails) {
            return "";
        }
        $spname = $this->PortDetails['serviceProviderName'] ?? "Unknown";
        $spid = $this->PortDetails['serviceProviderId'] ?? "9999";
        return " - $spname ($spid)";
    }

    public function addNPIS(NPISServiceQual $npis)
    {
        $port = $npis->getSupportingProduct($this->PortName);
        if ($port) {
            $this->PortDetails = $port;
        }
    }

    public mixed $supportingProduct = null;

    public function getDescription(): string
    {
        $retstr = $this->Status;
        return $retstr . " " . $this->getPortDetailsString();
    }
}
