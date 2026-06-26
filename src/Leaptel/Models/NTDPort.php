<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Leaptel\API\Components\NBNPortRecord;

class NTDPort extends Model
{
    public $timestamps = false;
    protected $table = 'location_ntdports';
    protected $guarded = [];
    protected $casts = ['raw' => 'array'];

    public static function getFromPortRecord(NBNPortRecord $p)
    {
        $key = [
            "location_id" => $p->location_id,
            "port_name" => $p->PortName,
        ];
        $d = $p->PortDetails;
        $rref = $d['resourceRef'] ?? [];
        $params = [
            "ntdid" => $p->NTDID,
            "avc_id" => null,
            "port_id" => $d['id'] ?? null,
            "service_provider_id" => $d['serviceProviderId'] ?? null,
            "service_provider_name" => $d['serviceProviderName'] ?? null,
            "resourceRef" => $rref[0] ?? null,
            "raw" => ["probj" => serialize($p)],
        ];
        if ($p->avc_id) {
            $params['avc_id'] = $p->avc_id;
        }
        $m = static::firstOrCreate($key, $params);
        $changed = false;
        foreach ($params as $k => $v) {
            if ($m->{$k} != $v) {
                $m->{$k} = $v;
                $changed = true;
            }
        }
        if ($changed) {
            $m->save();
        }
        return $m;
    }
}
