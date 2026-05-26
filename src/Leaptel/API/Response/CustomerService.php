<?php

namespace Leaptel\API\Response;

use Leaptel\API\Schemas\ResponseBase;

/**
 * @OA\Schema(description="Customer Service", type="object")
 * @package Leaptel
 */
class CustomerService extends ResponseBase
{
    /**
     * Service ID
     *
     * @var int
     * @OA\Property()
     */
    public int $service_id;

    // Appears to be the same as service_id
    protected string $sort_key;

    /**
     * Description
     *
     * @var string
     * @OA\Property()
     */
    public string $description;

    // Same as description
    protected string $wholesale_plan_description;
    protected string $retail_plan_description;

    /**
     * Customer ID
     *
     * @var int
     * @OA\Property()
     */
    public int $customer_id;

    /**
     * Product ID
     *
     * @var string
     * @OA\Property()
     */
    public string $product_id;

    /**
     * Type ID
     *
     * @var int
     * @OA\Property()
     */
    public int $type_id;

    /**
     * Start Date
     *
     * @var string
     * @OA\Property()
     */
    public string $start_date;

    /**
     * Finish Date (0000's mean 'not finished')
     *
     * @var string
     * @OA\Property()
     */
    public string $finish_date;

    // Unused
    protected string $contract_end;

    /**
     * Wholesale Plan ID
     *
     * @var int
     * @OA\Property()
     */
    public int $wholesale_plan_id;

    /**
     * Retail Plan ID
     *
     * @var int
     * @OA\Property()
     */
    public int $retail_plan_id;

    // Unused
    protected int $parent_service_id;

    /**
     * Service State ('active'/'inactive')
     *
     * @var string
     * @OA\Property()
     */
    public string $state;

    /**
     * Location ID
     *
     * @var string
     * @OA\Property()
     */
    public string $identifier;

    /**
     * Tag (? Vlan tag?)
     *
     * @var string
     * @OA\Property()
     */
    public string $tag;

    /**
     * Customer Name
     *
     * @var string
     * @OA\Property()
     */
    public string $customer_name;

    // Unused
    protected string $billing_day;
    protected string $customer_referral_program;

    /**
     * Wholesaler ID
     *
     * @var int
     * @OA\Property()
     */
    public int $wholesaler_id;

    /**
     * Wholesaler Name
     *
     * @var string
     * @OA\Property()
     */
    public string $wholesaler_name;

    // Unused
    protected string $allow_credit_card;
    protected string $allow_bank_account;
    protected string $bpay;
    protected string $minimum_invoice_amount;

    /**
     * Service Type Description(?)
     *
     * @var string
     * @OA\Property()
     */
    public string $st_description;

    /**
     * Service Group
     *
     * @var string
     * @OA\Property()
     */
    public string $service_group;

    // Unused
    protected string $retail_sub_type;
    protected ?string $parent_type_id;
    protected ?string $parent_state;

    /**
     * Raw Address
     *
     * @var string
     * @OA\Property()
     */
    public string $raw_address;

    /**
     * Service Address
     *
     * @var string
     * @OA\Property()
     */
    public string $service_address;

    /**
     * NBN Service Level Agreement
     *
     * @var string
     * @OA\Property()
     */
    public string $nbn_esla;

    /**
     * NTD ID
     *
     * @var string
     * @OA\Property()
     */
    public string $ntd_id;

    /**
     * NTD Port number
     *
     * @var string
     * @OA\Property()
     */
    public string $port_id;

    /**
     * Copper Pair ID
     *
     * @var string
     * @OA\Property()
     */
    public string $copperpair_id;

    /**
     * AVC ID
     *
     * @var string
     * @OA\Property()
     */
    public string $avc_id;

    /**
     * NTD Type
     *
     * @var string
     * @OA\Property()
     */
    public string $ntd_type;

    /**
     * NTD Version
     *
     * @var string
     * @OA\Property()
     */
    public string $ntd_version;

    /**
     * Service Speed
     *
     * @var string
     * @OA\Property()
     */
    public string $service_speed;

    /**
     * VLAN Tagging
     *
     * @var string
     * @OA\Property()
     */
    public string $vlan_tagging;
}
