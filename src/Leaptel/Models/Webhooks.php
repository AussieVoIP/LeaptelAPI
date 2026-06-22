<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Webhooks extends Model
{
    protected $table = 'webhooks';
    protected $guarded = [];
    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
        'server' => 'array',
    ];

    public static function storeRequest(Request $req)
    {
        $payload = $req->all();
        $wh = new self();
        $wh->payload = $payload;
        $wh->type = $payload['type'];
        $wh->ntype = $payload['notification_type'];
        $wh->service_id = $payload['service_id'] ?? null;
        $wh->order_id = $payload['order_id'] ?? null;
        $wh->headers = $req->headers->all();
        $wh->server = self::getServerVars();
        $wh->path = $req->path();
        $wh->save();
        return $wh;
    }

    private static function getServerVars(): array
    {
        return array_filter($_SERVER, function ($key) {
            return (strpos($key, 'HTTP') === 0 || strpos($key, 'SERVER') === 0);
        }, ARRAY_FILTER_USE_KEY);
    }
}
