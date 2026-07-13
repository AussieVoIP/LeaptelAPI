<?php

namespace Leaptel\API\Response;

use Leaptel\API\Schemas\ResponseBase;
use Leaptel\Models\NBNService;
use Leaptel\Models\ServiceOrder;

/**
 * @OA\Schema(description="Customer Order from Customers/CustomerOrders ", type="object")
 * @package Leaptel
 */
class CustomerOrder extends ResponseBase
{
    private ?ServiceOrder $somodel = null;

    protected function finishImport(array $row)
    {
        // Generate/update the model
        $m = $this->getServiceOrderModel();
    }

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

    public array $nbn_callbacks = [];

    /**
     * Well I'm sure this will not work.
     *
     * @return \DateTimeImmutable
     */
    public function getLatestEventTime(): \DateTimeImmutable
    {
        if (!$this->nbn_callbacks) {
            // Not sure how useful this will be...
            $this->nbn_callbacks = [["event_time" => $this->start]];
        }
        $event = new \DateTimeImmutable("2000-01-01 00:00:00");
        foreach ($this->nbn_callbacks as $row) {
            if (empty($row['event_time'])) {
                throw new \Exception("No et in row " . json_encode($row));
            }
            $ts = new \DateTimeImmutable($row['event_time']);
            if ($ts > $event) {
                $event = $ts;
            }
        }
        return $event;
    }

    public function getOrderDescription(): string
    {
        return "Order ID " . $this->order_id . " - " . $this->description;
        return $this->service_id . " (Order ID " . $this->order_id . ") - " . $this->description;
    }

    public function getOrderDisplayArray(NBNService $service): array
    {
        $timestamp = $this->timestamp ?? 0;
        $secs = time() - $timestamp;
        if ($secs < 5) {
            $age = "Fresh";
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
        $retarr = ["Latest" => $this->latest_comment];
        if (!empty($this->latest_provider_comment)) {
            $retarr["Provider"] = $this->latest_provider_comment;
        }
        if (!empty($this->latest_system_comment)) {
            $retarr["System"] = $this->latest_system_comment;
        }
        if (!empty($this->latest_non_system_comment)) {
            $retarr["Details"] = $this->latest_non_system_comment;
        }
        return $retarr;
    }

    public function getServiceOrderModel(): ServiceOrder
    {
        if ($this->somodel === null) {
            $m = ServiceOrder::fromCustomerOrder($this);
            if ($m) {
                $this->somodel = $m;
            } else {
                print "Why did I not get created?\n";
                exit;
            }
        }
        return $this->somodel;
    }

    public function usingServiceOrderModel(ServiceOrder $somodel): CustomerOrder
    {
        $this->somodel = $somodel;
        return $this;
    }

    public function getDescription(): string
    {
        if ($this->description === "service") {
            return "Outage";
        }
        return $this->service_type ?? "Unknown";
    }
}
