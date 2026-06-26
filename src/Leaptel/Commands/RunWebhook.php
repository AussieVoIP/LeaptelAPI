<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\Actions\ProcessWebhook;
use Leaptel\Models\NBNService;
use Leaptel\Models\Webhook;

class RunWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaptel:webhook {--process=} {--list=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process a received webhook by id (can be comma separated), or list for a service ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $list = $this->option("list");
        if ($list) {
            return $this->listWebhooks($list);
        }
        $ids = $this->option("process");
        $allids = explode(",", $ids); {
            foreach ($allids as $id) {
                $wh = Webhook::where("id", $id)->first();
                if (!$wh) {
                    print "Could not find webhook id $id\n";
                    continue;
                }
                $this->processWebhook($wh);
            }
        }
    }

    public function processWebhook(Webhook $wh)
    {
        $pwh = new ProcessWebhook($wh);
        $res = $pwh->result();
        var_dump((string) $res);
        exit;
    }


    public function listWebhooks(string $service)
    {
        $hooks = Webhook::where("service_id", $service)->get();
        print "Webhooks for $service:\n";
        $svc = NBNService::where("service_id", $service)->first();
        print json_encode($svc) . "\n";
        foreach ($hooks as $h) {
            print $h->id . ": " . $h->getHeader() . "\n";
            print "  Body: " . $h->getBody() . "\n";
            print "     " . json_encode($h->payload) . "\n";
        }
    }
}
