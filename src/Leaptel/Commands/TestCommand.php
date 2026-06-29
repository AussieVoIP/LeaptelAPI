<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\Addressify\Addressify;
use Leaptel\API\Customers;
use Leaptel\API\Customers\GetAllCustomers;
use Leaptel\API\Location\GetServiceQual;
use Leaptel\API\Request\SQ;
use Leaptel\API\ServiceAssurance\GetServiceAssuranceTests;
use Leaptel\Models\Location;
use Leaptel\NBNCo\Places;

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
        $p = Places::getPlacesDetail("LOC000001785734");
        var_dump($p);
        exit;
        $q = Places::getAutoComplete("1 grayson st, west glad");
        var_dump($q);
        exit;
        $cust = (new GetAllCustomers())->go();
        var_dump($cust);
        exit;
        $location = [
            "street_number" => 1,
            "street_name" => "Grayson St",
            "suburb" => "West Gladstone",
            "state" => "QLD",
            "postcode" => "4680",
        ];

        $ac = new Addressify("1 Grayson St", "QLD");
        var_dump($ac->go());
        exit;
        var_dump(Location::genPriKey($location));
        exit;

        $sq = new SQ();
        $sq->location_id = "LOC000172784710";
        $q = (new GetServiceQual($sq))->go();
        /** @var \Leaptel\API\Response\NBNSQResponse $q */
        foreach ($q->ntd_ports as $name => $p) {
            print "$name is " . json_encode($p) . "\n";
            var_dump($p->getPortDetailsString());
        }
        exit;
        var_dump($q->ntd_ports);

        exit;
    }
}
