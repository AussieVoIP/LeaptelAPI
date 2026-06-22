<?php

namespace Leaptel\API\Orders;

/** @package Leaptel\API */
class CreateNewL2NBN extends CreateNewNBN
{
    public function __construct(string $custid)
    {
        parent::__construct($custid);
        $this->order->layer_2 = "yes";
    }

    public function setSTag(string $stag)
    {
        $this->order->lvc_id = $stag;
        return $this;
    }

    public function setCTag(string $ctag)
    {
        $this->order->lvc_c_tag = $ctag;
        return $this;
    }

    public function setAuthDetails(string $username, string $password, string $realm)
    {
        throw new \Exception("No auth on layer2");
    }
}
