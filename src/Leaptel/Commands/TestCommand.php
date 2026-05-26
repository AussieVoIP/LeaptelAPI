<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\API\Customers;
use Leaptel\API\Customers\CreateCustomer;
use Leaptel\API\Customers\DeleteCustomer;
use Leaptel\API\CustServices;
use Leaptel\API\Orders;
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
        $c = new DeleteCustomer(124379);
        $c->go();
        exit;

        $cr = new CustRequest();
        $cr->first_name = "Test";
        $cr->last_name = "Customer 1";
        $cr->birthdate = "1980-01-01";
        $cr->email = "xrobau+testcust@gmail.com";
        $cr->mobile = "0402077155";
        $cr->address = "1 Grayson St";
        $cr->city = "West Gladstone";
        $cr->state = "QLD";
        $cr->postcode = "4680";
        $c = new CreateCustomer($cr);
        var_dump($c->go());
        exit;

        $o = new Orders();
        var_dump($o->go());
        exit;
        $sq = new SQ();
        $sq->street_number = 1;
        $sq->street_name = "Grayson St";
        $sq->suburb = "West Gladstone";
        $sq->state = "QLD";
        $sq->postcode = "4680";
        $qual = new ServiceQual($sq);
        var_dump($qual->go());
        exit;

        $c = new Customers();
        $res = $c->go();
        $dreamtilt = $res['102919'];
        $cs = new CustServices($dreamtilt);
        var_dump($cs->go());
        exit;

        $p = new Products();
        $callable = function (WholesalerProduct $p) {
            return ($p->handoff_type == 'Layer 3');
        };
        $p->addFilter($callable);
        $res = $p->go();
        var_dump($res);
    }
}
