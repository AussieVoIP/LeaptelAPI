<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\Actions\CleanupServices;
use Leaptel\Models\NBNService;

class CleanupServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaptel:cleanup {--custid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup services for custid';

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        $custid = $this->option("custid");
        if ($custid) {
            $ids = [$custid];
        } else {
            $ids = [];
            $services = NBNService::select('customer_id')->distinct()->get();
            foreach ($services as $s) {
                $ids[] = $s->customer_id;
            }
        }
        foreach ($ids as $c) {
            $now = NBNService::where('customer_id', $c)->count();
            $cleanup = new CleanupServices($c);
            $cleanup->go(false);
            $then = NBNService::where('customer_id', $c)->count();
            if ($now != $then) {
                print "Customer $c had $now, after update is $then\n";
            }
        }
    }
}
