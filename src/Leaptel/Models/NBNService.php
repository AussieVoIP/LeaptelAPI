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
            "location_id" => $cs->identifier,
            "portnum" => (int) $cs->port_id,
            "lvc_id" => null,
            "lvc_name" => null,
            "lvc_c_tag" => null,
        ];
        $params['avc_id'] = $cs->avc_id ?? null;
        $params['raw'] = json_encode($cs->toArray());
        if ($sd) {
            $params['alldetails'] = json_encode($sd->toArray());
        }
        $l2 = $cs->layer_2_details ?? null;
        if ($l2) {
            $params['lvc_id'] = $l2['lvc_id'];
            $params['lvc_name'] = $l2['lvc_name'];
            $params['lvc_c_tag'] = $l2['lvc_c_tag'];
            $ctag = CTagsMap::where(["lvc_id" => $params['lvc_id'], "ctag" => $params['lvc_c_tag']])->first();
            if (!$ctag) {
                throw new \Exception("CTAG " . json_encode($l2) . " does not exist, database error");
            }
            if ($ctag->customer_id != $cs->customer_id) {
                throw new \Exception("Ctag " . json_encode($ctag) . " is not owned by custid " . $cs->customer_id);
            }
            if ($ctag->service_id != $cs->service_id) {
                $ctag->service_id = $cs->service_id;
                $ctag->save();
            }
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

    public function isL2Service(): bool
    {
        return $this->lvc_c_tag !== null;
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

    public function isNbnService(): bool
    {
        return ($this->plan_id != 1758);
    }

    public function getServiceSpeed(): string
    {
        if ($this->plan_id == 1758) {
            return "Dark Fibre NNI";
        }
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
        if (empty($d->access_technology)) {
            return [];
        }

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
        $d = $this->getAllDetailsObj();
        if (empty($d->access_technology)) {
            return [];
        }
        if ($this->isL2Service()) {
            return $this->getL2TransitDetails($d);
        }
        return $this->getL3TransitDetails($d);
    }

    public function getL2TransitDetails(object $d)
    {
        return [
            "Type" => "Layer 2",
            "LVC ID" => $this->lvc_id . " (" . $this->lvc_name . ")",
            "C_Tag" => $this->lvc_c_tag,
            "CPE VLAN" => $d->vlan_tagging ?? "N/A",
        ];
    }

    public function getL3TransitDetails(object $d): array
    {
        $retarr = [
            "Username" => $d->ppoe_username,
            "Password" => $d->ppoe_password,
            "VLAN Tagged" => $d->vlan_tagging ?? "N/A",
            "CGNAT" => $d->cgnat ?? "N/A",
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
