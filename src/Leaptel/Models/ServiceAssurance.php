<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;
use Leaptel\API\Response\ServiceAssuranceResult;
use Leaptel\API\ServiceAssurance\GetServiceAssuranceTest;
use Ramsey\Uuid\Uuid;

class ServiceAssurance extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $table = 'svc_assurance_tests';
    protected $guarded = [];
    protected $casts = [
        'details' => 'array',
    ];

    public static function fromResult(ServiceAssuranceResult $res): ServiceAssurance
    {
        $o = self::where(["service_id" => $res->service_id, "test_id" => $res->test_id])->first();
        if (!$o) {
            $params = [
                "uuid" => Uuid::uuid7()->toString(),
                "service_id" => $res->service_id,
                "test_id" => $res->test_id,
                "request_status" => $res->request_status,
            ];
            $o = new self($params);
            $o->save();
        }
        $o->storeResult($res);
        return $o;
    }

    public static function getObject(string $service_id, string $test_id, bool $refresh = false): ServiceAssurance
    {
        $o = self::where(["service_id" => $service_id, "test_id" => $test_id])->first();
        if (!$o) {
            $params = [
                "uuid" => Uuid::uuid7()->toString(),
                "service_id" => $service_id,
                "test_id" => $test_id,
                "request_status" => "unknown",
            ];
            $o = new self($params);
            $o->save();
        }
        if ($refresh) {
            $o->request_status = "unknown";
            $o->object = null;
            $o->save();
        }
        return $o;
    }

    public function updateDetails(string $key, mixed $value)
    {
        $d = $this->details;
        if ($value === "__delete__") {
            unset($d[$key]);
        } else {
            $d[$key] = $value;
        }
        $this->details = $d;
        $this->save();
        return $this;
    }

    public function isComplete(): bool
    {
        return ($this->request_status == "completed");
    }

    public function updateResult(): ServiceAssuranceResult
    {
        $sar = $this->getResult(true);
        return $sar;
    }

    public function getResult(bool $refresh = false): ServiceAssuranceResult
    {
        if ($refresh) {
            $this->object = null;
        }
        if (!$this->object) {
            // Always refresh if there's no object locally.
            $sar = (new GetServiceAssuranceTest($this->service_id, $this->test_id))->go(true);
            $this->storeResult($sar);
        } else {
            $sar = unserialize($this->object);
        }
        return $sar;
    }

    public function storeResult(ServiceAssuranceResult $sar): ServiceAssurance
    {
        $this->object = serialize($sar);
        $this->request_status = $sar->request_status;
        $this->save();
        return $this;
    }
}
