<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Leaptel\Addressify\Objects\AddressInfo;
use Leaptel\API\Request\SQ;

class Location extends Model
{
    public $timestamps = true;
    public $incrementing = false;
    protected $table = 'locations';
    protected $keyType = 'string';
    protected $primaryKey = 'prikeyhash';
    protected $guarded = [];
    protected $casts = ['details' => 'array'];

    public static function getKeyValues(): array
    {
        return [
            "lot_no" => null,
            "unit" => null,
            "level" => null,
            "street_number" => null,
            "street_name" => true,
            "suburb" => true,
            "state" => true,
            "postcode" => true,
            "source" => true,
        ];
    }

    public static function genPriKey(array $params)
    {
        $hasharr = [];
        foreach (self::getKeyValues() as $k => $v) {
            if (!empty($params[$k])) {
                $hasharr[$k] = $params[$k];
            } elseif ($v === true) {
                throw new \Exception("Param $k is required but not provided in " . json_encode($params));
            }
        }
        $hash = hash('sha256', json_encode($hasharr));
        $retarr = [
            "params" => $hasharr,
            "prikeyhash" => $hash,
        ];
        return $retarr;
    }

    public static function fromAddressInfo(AddressInfo $ai)
    {
        $maps = [
            "unit" => "UnitNumber",
            "level" => "LevelNumber",
            "street_number" => "Number",
            "suburb" => "Suburb",
            "state" => "State",
            "postcode" => "Postcode",
            "source" => "Source",
            "street_line" => "StreetLine",
        ];
        $params = [];
        foreach ($maps as $k => $v) {
            $params[$k] = $ai->{$v};
        }
        $params["name"] = $ai->AddressFull;
        $params["street_name"] = $ai->Street . " " . $ai->StreetType;
        $params["details"] = ["aiobj" => serialize($ai)];
        $keymaps = self::genPriKey($params);
        $changed = false;
        $m = self::firstOrCreate(["prikeyhash" => $keymaps['prikeyhash']], $params);
        foreach ($params as $k => $v) {
            if ($m->{$k} != $v) {
                $m->{$k} = $v;
                $changed = true;
            }
        }
        if ($m->wasRecentlyCreated) {
            // print "Model created - " . json_encode($m) . "\n";
        }
        if ($changed) {
            // print "Model changed - " . json_encode($m) . "\n";
            $m->save();
        }
        return $m;
    }

    public function getAiObject(): AddressInfo
    {
        if (empty($this->details['aiobj'])) {
            throw new \Exception("No AddressInfo object found\n");
        }
        return unserialize($this->details['aiobj']);
    }

    public function getServiceQualObject(): SQ
    {
        $sq = new SQ();
        $params = [
            "lot_no" => "lot_no",
            "unit" => "unit",
            "level" => "level",
            "street_number" => "street_number",
            "street_name" => "street_name",
            "suburb" => "suburb",
            "state" => "state",
            "postcode" => "postcode",
        ];
        foreach ($params as $local => $sqval) {
            if ($this->{$local}) {
                $sq->{$sqval} = $this->{$local};
            }
        }
        return $sq;
    }
}
