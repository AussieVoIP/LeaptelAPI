<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Models\ServiceAssurance;
use Leaptel\Models\Webhook;

class SvcAssuranceDetails
{
    public function __invoke(Request $req, string $uuid)
    {
        $refresh = (bool) $req->input("refresh");
        $sa = ServiceAssurance::where("uuid", $uuid)->first();
        if (!$sa) {
            throw new \Exception("No SA for $uuid");
        }
        $arr = $sa->toArray();
        unset($arr['object']);
        $obj = $sa->getResult($refresh);
        print "<h1>SA Debug</h1>\n";
        print "<pre>\n";
        print "Model:\n";
        print json_encode($arr, JSON_PRETTY_PRINT) . "\n\n";
        $objarr = $obj->toArray();
        $raw = $objarr['raw_test_result'];
        unset($objarr['raw_test_result']);
        print "Parsed Response:\n";
        print json_encode($objarr, JSON_PRETTY_PRINT) . "\n\n";
        print "Raw response:\n";
        print json_encode(json_decode($raw, true), JSON_PRETTY_PRINT) . "\n\n";
        print "</pre>\n";
        print "<h2>Textarea with raw response</h2>\n";
        print "<p>Useful to copy-and-paste or whatever</p>\n";
        print "<textarea rows=24 cols=80>\n";
        print "$raw\n";
        print "</textarea>";
    }
}
