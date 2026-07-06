<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\Addressify\Addressify;
use Leaptel\API\Customers;
use Leaptel\API\Customers\GetAllCustomers;
use Leaptel\API\Customers\GetAllServicesForCustomer;
use Leaptel\API\Location\GetServiceQual;
use Leaptel\API\Request\SQ;
use Leaptel\API\Service\GetServiceDetails;
use Leaptel\API\ServiceAssurance\ServiceAssuranceHistory;
use Leaptel\API\ServiceAssurance\GetServiceAssuranceTest;
use Leaptel\API\ServiceAssurance\ServiceAssuranceTestTypes;
use Leaptel\Core\ServiceAssuranceManager;
use Leaptel\Models\Location;
use Leaptel\Models\ServiceAssurance;
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
        $test = ServiceAssuranceManager::getServiceAssuranceTestTypes();
        var_dump($test);
        exit;
        $all = ServiceAssuranceManager::getAllServiceAssuranceTests("215359");
        var_dump($all);
        exit;
        /*
        var_dump(ServiceAssuranceManager::updateIncompleteTests());
        exit;
        $me = "219655";
        $testid = "254708";
        // Nuke any that exist
        // ServiceAssurance::where(["service_id" => $me, "test_id" => $testid])->delete();

        $sa = ServiceAssuranceManager::getServiceAssuranceTest($me, $testid);
        print json_encode($sa, JSON_PRETTY_PRINT) . "\n";
        exit;
        var_dump($sa->getResult());
        exit;
        */
        $custs = (new GetAllCustomers())->go();
        foreach ($custs as $c) {
            $svcs = (new GetAllServicesForCustomer($c->id))->go();
            foreach ($svcs as $s) {
                $all = ServiceAssuranceManager::getAllServiceAssuranceTests($s->service_id);
                print json_encode($s) . " has " . $all->count() . "\n";
            }
        }
        exit;
        $me = "201756"; // FW
        $me = "219655"; // FTTP
        $test = ServiceAssuranceManager::getServiceAssuranceTestType("FTTP", "SH");
        $r = ServiceAssuranceManager::requestServiceAssurance($test, $me);
        print "I now have " . json_encode($r) . "\n";
        while (!$r->isComplete()) {
            print "It is not complete. Sleeping 2 seconds\n";
            sleep(2);
            $sar = $r->updateResult();
            print "Sar is now " . json_encode($sar) . "\n";
        }
        print "I am here\n";
        var_dump($r);
        exit;
        $core = ServiceAssuranceManager::getServiceAssuranceTestTypes("FTTP");
        var_dump($core);
        exit;
        $testid = "254526";
        $sat = (new GetServiceAssuranceTest($me, $testid))->go();
        var_dump($sat);
        exit;
        /*
        $s = (new GetServiceDetails($me))->go();
        var_dump($s);
        exit;
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
