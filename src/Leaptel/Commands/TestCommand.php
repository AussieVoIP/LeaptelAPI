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
        $t = new GetServiceAssuranceTests();
        $tests = $t->go(true);
        var_dump(array_keys($tests));
        exit;
        var_dump($t->go(true));
    }
}
