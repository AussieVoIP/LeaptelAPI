<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Models\Webhook;

class EventDetails
{
    public function __invoke(Request $req, string $uuid)
    {
        $wh = Webhook::where("uuid", $uuid)->first();
        if (!$wh) {
            throw new \Exception("No event found for $uuid");
        }
        // If there's no customer_id in the webhook, we don't care about
        // checking if it's valid.
        if ($wh->customer_id !== null) {
            $cid = $req->input("cid");
            if (!$cid) {
                throw new \Exception("No CID provided");
            }
            if ((string) $wh->customer_id !== (string) $cid) {
                throw new \Exception("Incorrect Customer ID $cid provided");
            }
        }
        print "<h1>Debug</h1>\n";
        print "<pre>\n";
        print json_encode($wh, JSON_PRETTY_PRINT) . "\n";
        print "</pre>\n";
    }
}
