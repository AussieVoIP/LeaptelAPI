<?php

namespace Leaptel\API\Response;

use App\Models\NBNService;
use Leaptel\API\Schemas\ResponseBase;

/**
 * @OA\Schema(description="Customer Order from Customers/CustomerOrders ", type="object")
 * @package Leaptel
 */
class CustomerOrder extends ResponseBase
{
    /**
     * Order ID
     *
     * @var int
     * @OA\Property()
     */
    public int $order_id;

    /**
     * Service ID
     *
     * @var int
     * @OA\Property()
     */
    public int $service_id;

    /**
     * Description
     *
     * @var string
     * @OA\Property()
     */
    public string $description;

    /**
     * Customer ID
     *
     * @var int
     * @OA\Property()
     */
    public int $customer_id;

    /**
     * Action
     *
     * @var string
     * @OA\Property()
     */
    public string $action;

    /**
     * Status
     *
     * @var string
     * @OA\Property()
     */
    public string $status;

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
     * Order State ('active'/'inactive')
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
     * Tag
     *
     * @var string
     * @OA\Property()
     */
    public string $tag;

    /**
     * Latest System Comment (May be blank)
     *
     * @var string
     * @OA\Property()
     */
    public string $latest_system_comment;

    /**
     * Latest Non-System Comment
     *
     * @var string
     * @OA\Property()
     */
    public string $latest_non_system_comment;

    /**
     * Latest Provider Comment (May be blank)
     *
     * @var string
     * @OA\Property()
     */
    public string $latest_provider_comment;

    /**
     * Timestamp when last requested
     *
     * @var int
     * @OA\Property()
     */
    public int $timestamp;


    public function getOrderDescription(): string
    {
        return $this->service_id . " (Order ID " . $this->order_id . ") - " . $this->description;
    }

    public function getOrderDisplayArray(NBNService $service): array
    {
        $timestamp = $this->timestamp ?? 0;
        $secs = time() - $timestamp;
        if ($secs < 5) {
            $age = "Recent";
        } else {
            $age = "$secs seconds ago";
        }
        $retarr = [
            "Service ID" => $this->service_id,
            "Service" => $service->getDisplayName(false),
            "Order Type" => $this->description,
            "Order Timestamp" => $this->start,
            "Cache Age" => $age,
        ];
        return $retarr;
    }

    public function getPreformatted(): array
    {
        $retarr = ["Latest Comment" => $this->latest_comment];
        if (!empty($this->latest_provider_comment)) {
            $retarr["Provider Comment"] = $this->latest_provider_comment;
        }
        return $retarr;
    }
}
