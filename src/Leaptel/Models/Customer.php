<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Leaptel\API\Response\CustomerResponse;

class Customer extends Model
{
    protected $table = 'customers';
    protected $guarded = [];

    public static function fromCustomerResponse(CustomerResponse $cr): Customer
    {
        $map = [
            "company_name" => "company_name",
            "first_name" => "first_name",
            "last_name" => "last_name",
            "birthdate" => "birthdate",
            "email" => "email",
            "mobile" => "mobile",
            "phone" => "phone",
            "fax" => "fax",
            "address1" => "address1",
            "address2" => "address2",
            "city" => "city",
            "state" => "state",
            "postcode" => "postcode",
            "active" => "active",
        ];
        $params = [];
        foreach ($map as $k => $v) {
            $params[$k] = $cr->{$v};
        }
        $changed = false;
        $m = Customer::firstOrCreate(['id' => $cr->customer_id], $params);
        foreach ($params as $k => $v) {
            if ($m->{$k} != $v) {
                $m->{$k} = $v;
                $changed = true;
            }
        }
        if ($changed) {
            $m->save();
        }
        return $m;
    }
}
