<?php

namespace Leaptel\Commands;

use Illuminate\Console\Command;
use Leaptel\API\ServiceAssurance\GetServiceAssuranceTests;

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
        $sa = (new GetServiceAssuranceTests())->go();
        var_dump($sa);
        exit;
    }
}
