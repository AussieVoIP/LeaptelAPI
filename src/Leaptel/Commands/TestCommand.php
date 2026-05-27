<?php

namespace Leaptel\Commands;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Console\Command;
use Leaptel\API\Customers;
use Leaptel\API\Customers\CreateCustomer;
use Leaptel\API\Orders\CreateNewNBN;
use Leaptel\API\Customers\CustomerOrders;
use Leaptel\API\Customers\DeleteCustomer;
use Leaptel\API\CustServices;
use Leaptel\API\Models\Component\OrderContact;
use Leaptel\API\Orders;
use Leaptel\API\Orders\CreateNewL2NBN;
use Leaptel\API\Products;
use Leaptel\API\Request\CustRequest;
use Leaptel\API\Request\SQ;
use Leaptel\API\Response\WholesalerProduct;
use Leaptel\API\ServiceQual;

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
        $c = new Customers();
        $res = $c->go();
        // Dreamtilt
        $dt = $res['102919'];
        // Aussie
        $dt = $res['100943'];

        $oc = new OrderContact();
        $oc->contact_first_name = "Rob";
        $oc->contact_last_name = "Thomas";
        $oc->contact_address = "1 Fake St";
        $oc->contact_suburb = "Brisbane";
        $oc->contact_state = "QLD";
        $oc->contact_postcode = "4000";
        $oc->contact_email = "xrobau+test@gmail.com";

        // $order = new CreateNewL2NBN($dt, "1001");
        $order = new CreateNewNBN($dt);
        $order->setOrderContact($oc);

        $order->setAuthDetails("authusername", "authpassword", "aussievoip.com.au");

        $after = new DateTimeImmutable('2030-01-01 00:00:01', new DateTimeZone('Australia/Brisbane'));
        $order->setOrderAfter($after);

        // Get the service qualification
        $sq = new SQ();
        $sq->street_number = 1;
        $sq->street_name = "Grayson St";
        $sq->suburb = "West Gladstone";
        $sq->state = "QLD";
        $sq->postcode = "4680";
        $qual = new ServiceQual($sq);
        $sqresp = $qual->go();

        $order->usingLocation($sqresp);

        $p = new Products();
        $res = $p->go();
        $plan = $res['TC4-L3-F-50-20'];

        $order->usingPlan($plan);

        $order->updateSelectedPortByName('1-UNI-D3');

        $req = $order->getOrderRequest();
        var_dump($req->toArray());
    }
}
