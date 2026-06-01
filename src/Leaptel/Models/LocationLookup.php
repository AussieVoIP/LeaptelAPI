<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;

class LocationLookup extends Model
{
    protected $table = 'location_lookup';
    protected $guarded = [];

    /**
     * @param string $req Must be in upper case
     * @param Location $l
     * @return LocationLookup
     */
    public static function storeLocationRef(string $req, Location $l): LocationLookup
    {
        $known = self::where(["source" => $req, "lochash" => $l->prikeyhash])->first();
        if (!$known) {
            $known = new LocationLookup(["source" => $req, "state" => $l->state, "lochash" => $l->prikeyhash]);
            $known->save();
        }
        return $known;
    }
}
