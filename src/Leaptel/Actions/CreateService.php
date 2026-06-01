<?php

namespace Leaptel\Actions;

use Leaptel\API\Response\CustomerResponse;

class CreateService
{
    public function __construct(
        public CustomerResponse $cust
    ) {
        print "thingy\n";
    }
}
