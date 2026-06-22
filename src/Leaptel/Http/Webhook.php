<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Actions\ProcessWebhook;
use Leaptel\Models\Webhooks;

class Webhook
{
    public function __invoke(Request $req)
    {
        $wh = Webhooks::storeRequest($req);
        \App\Slack\Debug::post("Webhook " . $wh->id . " to " . $wh->path, "New webhook:\n```" . json_encode($wh->payload) . "```\n");
        $pwh = new ProcessWebhook($wh);
        return $pwh->result();
    }
}
