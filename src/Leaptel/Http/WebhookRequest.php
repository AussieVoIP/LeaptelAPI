<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Actions\ProcessWebhook;
use Leaptel\Models\Webhook;

class WebhookRequest
{
    public function __invoke(Request $req)
    {
        // Multiple can be returned for an outage
        $whooks = Webhook::storeRequest($req);
        // See ProcessWebhook for how to handle incoming webhooks
        $results = [];
        foreach ($whooks as $wh) {
            \App\Slack\Debug::post("Webhook " . $wh->id . " to " . $wh->path, "New webhook:\n```" . json_encode($wh->payload) . "```\n");
            $pwh = new ProcessWebhook($wh);
            $results[] = $pwh->result();
        }
        return $results;
    }
}
