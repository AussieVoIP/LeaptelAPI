<?php

namespace Leaptel\API\Orders;

use Leaptel\API\Response\CustomerResponse;

/** @package Leaptel\API */
class CreateNewL2NBN extends CreateNewNBN
{
    public function __construct(CustomerResponse $cust, string $lvc_id = "44", string $lvc_c_tag = "0")
    {
        parent::__construct($cust);
        $this->order->layer_2 = "yes";
        $this->order->lvc_id = $lvc_id;
        $this->order->lvc_c_tag = $lvc_c_tag;
    }

    /**
     * Update the C tag if it was created incorrectly
     *
     * @param string $lvc_c_tag
     * @return CreateNewL2NBN
     */
    public function updateL2IDs(string $lvc_id, string $lvc_c_tag): CreateNewL2NBN
    {
        $this->order->lvc_id = $lvc_id;
        $this->order->lvc_c_tag = $lvc_c_tag;
        return $this;
    }

    public function setAuthDetails(string $username, string $password, string $realm)
    {
        throw new \Exception("No auth on layer2");
    }
}
