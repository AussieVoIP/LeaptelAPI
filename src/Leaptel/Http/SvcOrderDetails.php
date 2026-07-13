<?php

namespace Leaptel\Http;

use Illuminate\Http\Request;
use Leaptel\Models\ServiceAssurance;
use Leaptel\Models\ServiceOrder;
use Leaptel\Models\Webhook;

class SvcOrderDetails
{
    public function __invoke(string $orderhash)
    {
        $so = ServiceOrder::where("orderhash", $orderhash)->first();
        $co = unserialize($so->object);
        $arr = $so->toArray();
        unset($arr['object']);
        print "<h1>SO Debug</h1>\n";
        print "<pre>\n";
        print "Model:\n";
        print json_encode($arr, JSON_PRETTY_PRINT) . "\n\n";
        print "Customer Order:\n";
        print json_encode($co, JSON_PRETTY_PRINT) . "\n\n";
        print "</pre>\n";
        return;
    }
}
