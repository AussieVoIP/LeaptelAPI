<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\Addressify\Addressify;
use Leaptel\API\Customers;
use Leaptel\API\Customers\GetAllCustomers;
use Leaptel\API\Location\GetServiceQual;
use Leaptel\API\Request\SQ;
use Leaptel\API\Service\GetServiceDetails;
use Leaptel\API\ServiceAssurance\ServiceAssuranceHistory;
use Leaptel\API\ServiceAssurance\ServiceAssuranceTestTypes;
use Leaptel\Models\Location;
use Leaptel\Models\Webhook;
use Leaptel\NBNCo\Places;
use Ramsey\Uuid\Uuid;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ttt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Command';

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        $me = "219655";
        $me = "201756";
        /*
        $s = (new GetServiceDetails($me))->go();
        var_dump($s);
        exit;
        $c = (new GetAllCustomers())->go();
        var_dump($c);
        exit;
        $t = (new ServiceAssuranceTestTypes())->go();
        var_dump($t);
        exit;
        */
        $h = (new ServiceAssuranceHistory($me))->go();
        var_dump($h);
        exit;
        print json_encode($h) . "\n";
        exit;
        var_dump($h);
        exit;
        var_dump($t->go(true));
    }
}
