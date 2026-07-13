<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Leaptel\API\Response\CustomerOrder;

class ServiceOrder extends Model
{
    protected $table = 'service_order_history';
    protected $guarded = [];
    protected $casts = [
        'details' => 'array',
    ];

    public static function fromCustomerOrder(CustomerOrder $co): static
    {
        if (!$co->request_hash) {
            var_dump($co);
            throw new \Exception("No request hash, how?");
        }
        $params = ["order_id" => $co->order_id, "orderhash" => $co->request_hash];
        $m = self::where($params)->first();
        if (!$m) {
            // Need to create a new one!
            $m = new self($params);
            $m->service_id = $co->service_id;
            $m->description = $co->getDescription();
            $m->customer_id = $co->customer_id;
            $m->action = $co->action;
            $m->status = $co->status;
            $m->object = serialize($co);
            $m->details = ["body" => $co->__orig_row];
            $m->save();
            $co->usingServiceOrderModel($m);
        }
        ServiceOrderStatus::setOrderStatus($m);
        return $m;
    }

    public function getHumanStatus(): string
    {
        switch ($this->status) {
            case "closed":
                return "Completed";
            case "on hold":
                return "On Hold";
        }
        return "Fix ServiceOrder - Unknown " . $this->status;
    }

    public function getCustomerOrder(): CustomerOrder
    {
        return unserialize($this->object);
    }

    public function getDescription(): string
    {
        return "Order ID " . $this->order_id . " " . $this->description . " (" . $this->getHumanStatus() . ")";
    }

    public function getDetailsUrl(): string
    {
        return route("sodetails", ["orderhash" => $this->orderhash]);
    }
}
