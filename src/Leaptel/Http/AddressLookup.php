<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Addressify\Addressify;
use Leaptel\NBNCo\Places;

class AddressLookup
{
    private $useNbnco = true;

    public function __invoke(Request $req)
    {
        $query = $req->input("q");
        $withraw = !empty($req->input("withraw"));
        $refresh = !empty($req->input("refresh"));
        if ($this->useNbnco) {
            $resp = $this->nbnLookup($query, $withraw, $refresh);
        } else {
            $resp = $this->addrLookup($query, $withraw, $refresh);
        }
        return response()->json($resp);
    }

    public function nbnLookup(string $query, bool $withraw, bool $refresh): array
    {
        $resp = [
            "query" => $query,
            "source" => "places",
            "result" => [],
        ];
        $style = "max-width: 30em";
        $ttclass = "tooltip rounded bg-white p-1 shadow-lg";
        $suggestions = Places::getAutoComplete($query);
        foreach ($suggestions as $s) {
            $desc = $s->formattedAddress;
            // Add tooltip for long ones
            if (strlen($desc) > 50) {
                $mid = "<div class='truncate has-tooltip' style='$style'><span class='$ttclass'>$desc</span>$desc</div>";
            } else {
                $mid = "<div class='truncate' style='$style'>$desc</div>";
            }
            $valid = true;
            if ($s->source !== "lapi") {
                $display = "<div class='flex justify-between'>$mid<div>Error (" . ucfirst($s->source) . ")</div></div>";
                $valid = false;
            } else {
                $display = "<div class='flex justify-between'><div class='truncate' style='$style'>$desc</div><div class='font-mono'>" . $s->id . "</div></div>";
            }
            $row = [
                "display" => $display,
                "desc" => $desc,
                "fulldesc" => $s->formattedAddress,
                "source" => "places",
                "hash" => $s->id,
                "details" => "places",
                "valid" => $valid,
            ];
            $resp["result"][] = $row;
        }
        return $resp;
    }

    public function addrLookup(string $query, bool $withraw, bool $refresh)
    {
        $a = new Addressify($query);
        $raw = $a->go($refresh);
        $resp = [
            "query" => $query,
            "source" => "addressify",
            "result" => [],
        ];
        if ($withraw) {
            $resp['raw'] = $raw;
        }
        foreach ($raw as $name => $loc) {
            $resp["result"][] = [
                "desc" => $name,
                "fulldesc" => $loc['name'],
                "hash" => $loc['prikeyhash'],
                "details" => $loc['details'],
            ];
        }
        return $resp;
    }
}
