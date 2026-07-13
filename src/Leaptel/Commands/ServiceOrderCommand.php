<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\Actions\CleanupServices;
use Leaptel\API\Customers\GetAllCustomers;
use Leaptel\API\Customers\GetAllServicesForCustomer;
use Leaptel\API\Service\GetAllServiceOrders;
use Leaptel\Models\NBNService;

class ServiceOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaptel:so {--custid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all service orders, optionally limited by custid';

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        $custid = $this->option("custid");
        if ($custid) {
            $custs = [$custid];
        } else {
            $custs = array_keys((new GetAllCustomers())->go());
        }
        foreach ($custs as $cid) {
            $svcs = (new GetAllServicesForCustomer($cid))->go();
            foreach ($svcs as $s) {
                $o =  (new GetAllServiceOrders($s->service_id))->go();
                print "Service " . $s->service_id . " found " . count($o) . " service orders, ids " . join(",", array_keys($o)) . "\n";
            }
        }
        exit;
    }
}
