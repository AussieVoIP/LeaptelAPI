<?php

namespace Leaptel\Actions;

use App\Actions\IncomingWebhook;
use Leaptel\Models\Webhook;

class ProcessWebhook
{
    public function __construct(
        public Webhook $wh
    ) {}

    public function result()
    {
        // You want to change this to something you control. It should send emails/sms/whatever
        if (!class_exists(IncomingWebhook::class)) {
            throw new \Exception("This needs to be implemented");
        }
        $a = new IncomingWebhook($this->wh);
        $result = $a->process();
        return response()->json(["status" => "complete", "id" => $this->wh->id, "result" => $result]);
    }
}
