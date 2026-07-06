<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class Webhook extends Model
{
    public $incrementing = false;
    protected $table = 'webhooks';
    protected $guarded = [];
    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
        'server' => 'array',
    ];
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected ?NBNService $svc = null;

    public static function storeRequest(Request $req): array
    {
        $uuid = Uuid::uuid7()->toString();
        $payload = $req->all();
        $retarr = [];
        $wh = new self(["uuid" => $uuid]);
        $wh->payload = $payload;
        $wh->type = $payload['type'];
        $wh->ntype = $payload['notification_type'];
        $wh->headers = $req->headers->all();
        $wh->server = self::getServerVars();
        $wh->path = $req->path();
        if ($wh->type == "ProductOrder") {
            $wh->service_id = $payload['service_id'] ?? null;
            $wh->order_id = $payload['order_id'] ?? null;
            $wh->save();
            $retarr[] = $wh;
        } elseif ($wh->type == "OutageEvent") {
            $wh->order_id = $payload['outage_id'];
            $scope = $payload['scope'];
            $services = $scope['service_id'] ?? [];
            foreach ($services as $sid) {
                $o = new self($wh->toArray());
                $o->service_id = $sid;
                $o->save();
                $retarr[] = $o;
            }
            return $retarr;
        } else {
            $wh->service_id = "UNKNOWN";
            $wh->order_id = "UNKNOWN";
            $wh->save();
            $retarr[] = $wh;
            return $retarr;
        }
        throw new \Exception("Never reached");
    }

    private static function getServerVars(): array
    {
        return array_filter($_SERVER, function ($key) {
            return (strpos($key, 'HTTP') === 0 || strpos($key, 'SERVER') === 0);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getNbnService(): ?NBNService
    {
        if (!$this->svc) {
            $this->svc = NBNService::where("service_id", $this->service_id)->first();
        }
        return $this->svc;
    }

    public function updateCustomerId(): ?bool
    {
        $svc = $this->getNbnService();
        if (!$svc) {
            return null;
        }
        $custid = $svc->customer_id;
        if ($this->customer_id !== $custid) {
            $this->customer_id = $custid;
            $this->save();
            return true;
        }
        return false;
    }

    public function getCustDetails(bool $throw = true): ?CustomerDetails
    {
        $n = $this->getNbnService();
        if (!$n) {
            if ($throw) {
                throw new \Exception("No nbn service found for " . $this->service_id);
            }
            // else
            return null;
        }
        $cd = CustomerDetails::getByCustID($n->customer_id);
        return $cd;
    }

    public function getHeader(): string
    {
        $ret = $this->ntype;
        if ($this->service_id) {
            $svc = $this->getNbnService();
            if (!$svc) {
                $ret .= " (Unknown Address for " . $this->service_id . "!)";
            } else {
                $ret .= " (" . $svc->getDisplayName(false, false) . ")";
            }
        }
        return $ret;
    }

    public function getBody(): string
    {
        switch ($this->type) {
            case "ProductOrder":
                return $this->getProductOrderBody();
            case "OutageEvent":
                return $this->getOutageEventBody();
        }
        return "Unknown " . json_encode($this);
    }

    public function getEventTime(): ?string
    {
        return $this->payload['event_time'] ?? null;
    }

    public function getProductOrderBody(): string
    {
        // $ret = "Order " . $this->order_id . " " . $this->ntype;
        $ret = $this->ntype;
        $event_time = $this->getEventTime();
        if ($event_time) {
            $ret .= " at $event_time";
        }
        $ret .= " for service " . $this->service_id;
        return $ret;
    }

    public function getOutageEventBody(): string
    {
        $ret = str_replace("\n", " ", $this->payload['information']);
        return $ret;
    }

    /**
     * This should be used by the ProcessWebhook tool that you provide
     *
     * @return array
     */
    public function getDeliveryDetails(): array
    {
        $ret = [
            "sendsms" => false,
            "sms" => [
                "smsfrom" => "NBN.info",
                "smsto" => null,
            ],
            "sendemail" => false,
            "email" => [
                "emailfrom" => "info@example.com",
                "emailto" => null,
                "emailview" => "defaultemail",
            ],
        ];
        return $ret;
    }

    public function getHumanHeader(): string
    {
        $event_time = $this->getEventTime();
        if ($event_time) {
            $ret = $event_time;
        } else {
            $ret = "(Err Timestamp)";
        }
        return $ret . " " . $this->type;
    }

    public function getHumanString()
    {
        $ret = $this->getBody() . " -- " . join("/", [$this->type, $this->ntype]);
        return $ret;
        $ret = $this->getBody() . " -- " . join("/", [$this->type, $this->ntype]);
        $ret .= " event state " . $this->state . " at " . $this->getEventTime();
        $ret .= " debug " . json_encode($this);
        return $ret;
    }

    public function getDetailsUrl(): ?string
    {
        $sid = $this->service_id;
        if (!$sid) {
            return null;
        }
        $params = ["uuid" => $this->uuid];
        if ($this->customer_id) {
            $params["cid"] = $this->customer_id;
        }
        return route("eventdetails", $params);
    }
}
