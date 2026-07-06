<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrderStatus extends Model
{
    public $incrementing = false;
    protected $table = 'service_order_status';
    protected $primaryKey = 'order_id';
    protected $guarded = [];
    protected $casts = [
        'details' => 'array',
        'event_time' => 'immutable_datetime',
    ];

    public static function setOrderStatus(ServiceOrder $so): static
    {
        $params = ["order_id" => $so->order_id];
        $s = self::firstOrNew($params);
        $co = $so->getCustomerOrder();
        $event_time = $co->getLatestEventTime();
        if ($s->event_time < $event_time) {
            print "Making it\n";
            $s->event_time = $event_time;
            $s->action = $co->action;
            $s->status = $co->status;
            $s->description = $co->service_type;
            $s->details = ["latest_hash" => $co->request_hash, "known" => [$co->request_hash => $co->status]];
            $s->save();
        }
        $details = $s->details;
        if (empty($details['known'][$co->request_hash])) {
            print "updating details\n";
            $details['known'][$co->request_hash] = $co->status;
            $s->details = $details;
            $s->save();
        }
        return $s;
    }
}
