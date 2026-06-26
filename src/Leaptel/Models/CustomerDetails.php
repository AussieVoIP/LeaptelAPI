<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDetails extends Model
{
    public $timestamps = false;
    protected $table = 'customer_details';
    protected $casts = ['details' => 'array'];
    protected $primaryKey = 'customer_id';
    protected $guarded = [];

    public static function getByCustID(string $custid): CustomerDetails
    {
        $cd = static::where("customer_id", $custid)->first();
        if (!$cd) {
            $cd = new static(["customer_id" => $custid]);
            $c = Customer::where("id", $custid)->first();
            if (!$c) {
                throw new \Exception("No customer record for $custid");
            }
            $displayname = $c->company_name . " (" . $c->first_name . " " . $c->last_name . ")";
            $cd->display_name = $displayname;
            $cd->email_contact = $c->email;
            $cd->phone_contact = $c->mobile;
            $cd->emaildest_override = $c->email;
            $cd->smsdest_override = $c->mobile;
            $cd->save();
        }
        return $cd;
    }
}
