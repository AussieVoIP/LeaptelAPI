<?php

namespace Leaptel\Actions;

use Leaptel\Models\Webhooks;

class ProcessWebhook
{
    public function __construct(
        public Webhooks $wh
    ) {}

    public function result()
    {
        return response()->json(["status" => "complete", "id" => $this->wh->id]);
    }
}
