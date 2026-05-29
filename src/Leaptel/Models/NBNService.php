<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Leaptel\API\Response\CustomerService;
use Leaptel\API\Response\ServiceDetails;

class NBNService extends Model
{
    protected $table = 'service_details';
    protected $guarded = [];

    public static function updateDb(CustomerService $cs, ?ServiceDetails $sd = null): NBNService
    {
        $changed = false;
        $params = [
            "customer_id" => $cs->customer_id,
            "product_id" => $cs->product_id,
            "plan_id" => $cs->wholesale_plan_id,
            "start_date" => $cs->start_date,
            "location_id" => $cs->identifier
        ];
        $params['avc_id'] = $cs->avc_id ?? null;
        $params['raw'] = json_encode($cs->toArray());
        if ($sd) {
            $params['alldetails'] = json_encode($sd->toArray());
        }

        $m = self::firstOrCreate(['service_id' => $cs->service_id], $params);
        foreach ($params as $k => $v) {
            if ($m->{$k} != $v) {
                $m->{$k} = $v;
                $changed = true;
            }
        }

        if ($m->wasRecentlyCreated) {
            print "Model created - " . json_encode($m) . "\n";
        }
        if ($changed) {
            print "Model changed - " . json_encode($m) . "\n";
            $m->save();
        }
        return $m;
    }

    public function getRawObj(bool $array = false)
    {
        $raw = null;
        if ($raw === null) {
            $raw = json_decode($this->raw, $array);
        }
        return $raw;
    }

    public function getAllDetailsObj(bool $array = false)
    {
        $details = null;
        if ($details === null) {
            $details = json_decode($this->alldetails, $array);
        }
        return $details;
    }

    public function getDisplayName(bool $withsid = true): string
    {
        $r = $this->getRawObj();
        $displayname = $r->raw_address . " (" . $r->state . ")";
        if ($withsid) {
            return $this->service_id . " - $displayname";
        }
        return $displayname;
    }

    public function getPlanDescription(): string
    {
        $raw = $this->getRawObj();
        return $raw->description;
    }

    public function getServiceSpeed(): string
    {
        $raw = $this->getRawObj();
        return $raw->service_speed;
    }

    public function getNtdDetails(): array
    {
        $raw = $this->getRawObj();
        $d = $this->getAllDetailsObj();
        if (empty($d->poi)) {
            $poiresult = "Unknown POI Information";
        } else {
            $poi = $d->poi;
            $poiresult = $poi->poi_name . " (" . $poi->poi_identifier . ")";
        }

        $type = $raw->ntd_type ?? "Unknown";
        $nvers = $raw->ntd_version ?? "Unknown";
        $retarr = [
            "CPE Type" => $d->access_technology . " (" . $d->access_type . ")",
            "Port No" => $raw->port_id,
            "NTD Type" => "$type ($nvers)",
            "NTD ID" => $raw->ntd_id,
            "POI" => $poiresult,
        ];
        return $retarr;
    }

    public function getTransitDetails(): array
    {
        $raw = $this->getRawObj();
        $d = $this->getAllDetailsObj();
        $retarr = [
            "Username" => $d->ppoe_username,
            "Password" => $d->ppoe_password,
            "VLAN Tagged" => $d->vlan_tagging,
            "CGNAT" => $d->cgnat,
        ];
        if (!empty($d->staticip)) {
            $retarr['Static IP'] = $d->staticip;
        }
        if (!empty($d->staticipv6)) {
            $retarr['IPv6 Alloc'] = $d->staticipv6;
        }
        return $retarr;
    }
}
