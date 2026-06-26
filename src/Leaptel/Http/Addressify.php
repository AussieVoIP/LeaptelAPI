<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Addressify\AutoComplete;

class Addressify
{
    public function __invoke(Request $req)
    {
        $query = $req->input("q");
        $withraw = !empty($req->input("withraw"));
        $refresh = !empty($req->input("refresh"));
        $a = new AutoComplete($query);
        $raw = $a->go($refresh);
        $resp = [
            "query" => $query,
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
        return response()->json($resp);
    }
}
