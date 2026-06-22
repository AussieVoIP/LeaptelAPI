<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;

class NBNLVC extends Model
{
    public $timestamps = false;
    protected $table = 'nbnlvc';
    protected $guarded = [];

    public function getDetails(): array
    {
        $retarr = [
            "LVC " . $this->lvc_id . " (" . $this->lvc_name . ")",
            "  Description: " . $this->description,
            "        S_Tag: " . $this->s_tag,
        ];
        $pois = CTagsMap::getPoiKeys();
        $found = [];
        foreach ($pois as $name => $key) {
            if (!empty($this->{$key})) {
                $found[] = $name;
            }
        }
        if (!$found) {
            $retarr[] = "  ** Warning: No POIs assigned";
        } else {
            $retarr[] = "  Active POIs: " . join(" ", $found);
        }
        return $retarr;
    }
}
