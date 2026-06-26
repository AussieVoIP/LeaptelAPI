<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Actions\ProcessWebhook;
use Leaptel\Addressify\AutoComplete;
use Leaptel\Models\Webhook;

class Addressify
{
    public function __invoke(Request $req)
    {
        $query = $req->input("q");
        $a = new AutoComplete($query);
        $raw = $a->go(true);
        $resp = [
            "query" => $query,
            "result" => [],
            "raw" => $raw,
        ];
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
