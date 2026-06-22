<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Leaptel\RawDB;

class CTagsMap extends Model
{
    public $timestamps = false;
    protected $table = 'ctagmaps';
    protected $guarded = [];

    public static function getPoiKeys()
    {
        $pois = [
            "QLD" => "poi_qld",
            "NSW" => "poi_nsw",
            "ACT" => "poi_act",
            "VIC" => "poi_vic",
            "TAS" => "poi_tas",
            "SA" => "poi_sa",
            "NT" => "poi_nt",
            "WA" => "poi_wa"
        ];
        return $pois;
    }

    public static function getCtagSummary(string $lvc_id)
    {
        $pdo = RawDB::getPdo();
        $fetchmode = \PDO::FETCH_ASSOC;
        $total = $pdo->query("select count(1) as c from ctagmaps where lvc_id='$lvc_id'")->fetchAll($fetchmode);
        $retarr = [
            "count" => $total[0]['c'],
            "ctags" => [],
            "custid" => [],
        ];
        $all = $pdo->query("select * from ctagmaps where lvc_id='$lvc_id'")->fetchAll($fetchmode);
        foreach ($all as $row) {
            $ctag = $row['ctag'];
            $retarr["ctags"][$ctag] = $row;
            $custid = $row['customer_id'] ?? 1;
            $retarr["custid"][$custid][$ctag] = $row;
        }
        return $retarr;
    }

    public function getDescription(): string
    {
        if ($this->ipoe) {
            return "IPOE CTAG VLAN " . $this->ctag;
        }
        return "PPPoE CTAG VLAN " . $this->ctag;
    }
}
