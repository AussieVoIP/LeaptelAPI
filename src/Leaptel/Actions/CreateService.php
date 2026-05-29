<?php

namespace Leaptel\Actions;

use Leaptel\API\Response\Customer;

class CreateService
{
    public function __construct(
        public Customer $cust
    ) {
        print "thingy\n";
    }
}
