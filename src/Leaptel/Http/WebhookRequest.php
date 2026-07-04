<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Actions\ProcessWebhook;
use Leaptel\Models\Webhook;

class WebhookRequest
{
    public function __invoke(Request $req)
    {
        $all = $req->all();
        // \App\Slack\Debug::post("Webhook inbound", "``" . json_encode($all) . "```\n");
        // Multiple can be returned for an outage
        try {
            $whooks = Webhook::storeRequest($req);
            // See ProcessWebhook for how to handle incoming webhooks
            $results = [];
            foreach ($whooks as $wh) {
                \App\Slack\Debug::post("Webhook " . $wh->uuid . " to " . $wh->path, "New webhook:\n```" . json_encode($wh->payload) . "```\n");
                $pwh = new ProcessWebhook($wh);
                $results[] = $pwh->result();
            }
            return $results;
        } catch (\Exception $e) {
            \App\Slack\Debug::post("Webhook inbound FAILED " . $e->getMessage(), "```" . json_encode($all) . "```\n");
            return response("Please try again , error processing " . json_encode($all) . " - error " . $e->getMessage(), 500);
        }
    }
}
