<?php

namespace Leaptel\API\Request;

use Leaptel\API\Schemas\RequestBase;
use Leaptel\API\Traits\ContactTrait;

/**
 * @OA\Schema(description="Order Request", type="object")
 * @package Leaptel
 */
class OrderRequest extends RequestBase
{
    // This provides contact_(first_name, last_name, email, etc)
    use ContactTrait;

    /**
     * Customer ID
     *
     * @var integer
     * @OA\Property()
     */
    public int $customer_id;

    /**
     * Carrier ('nbn' or 'opticomm')
     *
     * @var string
     * @OA\Property()
     */
    public string $carrier;

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
    public string $avc_id = "";

    /**
     * Order Type ('data')
     *
     * @var string
     * @OA\Property()
     */
    public string $order_type = "data";

    /**
     * Plan ID
     *
     * @var integer
     * @OA\Property()
     */
    public int $plan_id;

    /**
     * Order After timestamp
     *
     * @var string
     * @OA\Property()
     */
    public string $order_after;

    /**
     * Connection Type ('new','transfer')
     *
     * @var string
     * @OA\Property()
     */
    public string $connection_type;

    /**
     * NTD Type
     *
     * @var string
     * @OA\Property()
     */
    public string $ntd_type;

    /**
     * NTD ID from Qualification
     *
     * @var string
     * @OA\Property()
     */
    public string $ntd_id;

    /**
     * NTD Port
     *
     * @var integer
     * @OA\Property()
     */
    public int $ntd_port;

    /**
     * Copper pair ID
     *
     * @var string
     * @OA\Property()
     */
    public string $copper_pair;

    /**
     * Layer 2 ('yes', 'no')
     *
     * @var string
     * @OA\Property()
     */
    public string $layer_2;

    /**
     * LVC ID
     *
     * @var string
     * @OA\Property()
     */
    public string $lvc_id;

    /**
     * LVC C Tag for L2 services
     *
     * @var string
     * @OA\Property()
     */
    public string $lvc_c_tag;

    /**
     * VLAN Tagging of service ('yes', 'no')
     *
     * @var string
     * @OA\Property()
     */
    public string $vlan_tagging;

    /**
     * Service Username
     *
     * @var string
     * @OA\Property()
     */
    public string $username;

    /**
     * Service Password
     *
     * @var string
     * @OA\Property()
     */
    public string $password;

    /**
     * Realm
     *
     * @var string
     * @OA\Property()
     */
    public string $realm;

    /**
     * Product ID
     *
     * @var string
     * @OA\Property()
     */
    public string $product_id;
}
