<?php

namespace Leaptel\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Leaptel\API\Response\ServiceAssuranceResult;
use Leaptel\API\ServiceAssurance\GetServiceAssuranceTest;
use Ramsey\Uuid\Uuid;

/**
 * Primary interface for ServiceAssurance tests
 *
 * @package Leaptel\Models
 */
class ServiceAssurance extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    private ?ServiceAssuranceResult $sar = null;

    protected $table = 'svc_assurance_tests';
    protected $guarded = [];
    protected $casts = [
        'details' => 'array',
    ];

    public function getTimeAsString(?\DateTimeImmutable $dt = null): string
    {
        if (!$dt) {
            return "";
        }
        return $dt->format('Y-m-d H:i:s');
    }

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

    /**
     * The name from the result - eg, 'Service Health'
     *
     * @return string
     */
    public function getTestName(): string
    {
        // If there is not an object, return the test id
        if (!$this->object) {
            return "Test ID " . $this->test_id;
        }
        $res = $this->getResult();
        return $res->test_name;
    }

    /**
     * DTI of when it was requested, or, the model was created
     *
     * @return DateTimeImmutable
     */
    public function getCreatedTime(): DateTimeImmutable
    {
        // If there is not an object, return the row timestamp created time
        if (!$this->object) {
            $dt = new \DateTimeImmutable($this->created_at);
        } else {
            $res = $this->getResult();
            $dt = new \DateTimeImmutable($res->requested_dt);
        }
        return $dt;
    }

    /** @return string */
    public function getCreatedTimeAsString()
    {
        return $this->getTimeAsString($this->getCreatedTime());
    }

    /** @return bool */
    public function isComplete(): bool
    {
        return ($this->request_status == "completed");
    }

    /**
     * Force a re-get of the SAR object from upstream
     *
     * @return ServiceAssuranceResult
     */
    public function updateResult(): ServiceAssuranceResult
    {
        $sar = $this->getResult(true);
        return $sar;
    }

    /**
     * Return the cached, or updated SAR object, fetching if neccesary.
     *
     * @param bool $refresh
     * @return ServiceAssuranceResult
     */
    public function getResult(bool $refresh = false): ServiceAssuranceResult
    {
        if ($refresh) {
            $this->object = null;
            $this->sar = null;
        }
        if (!$this->sar) {
            if (!$this->object) {
                // Always refresh if there's no object locally.
                $this->sar = (new GetServiceAssuranceTest($this->service_id, $this->test_id))->go(true);
                // Note that this sets $this->object, but not $this->sar, as it's set above.
                $this->storeResult($this->sar);
            } else {
                $this->sar = unserialize($this->object);
            }
        }
        return $this->sar;
    }

    /**
     * Store the result. Note that this does NOT update $this->sar, because there
     * really shouldn't be side-effects on things like this.
     *
     * @param ServiceAssuranceResult $sar
     * @return ServiceAssurance
     */
    public function storeResult(ServiceAssuranceResult $sar): ServiceAssurance
    {
        $this->object = serialize($sar);
        $this->request_status = $sar->request_status;
        $this->save();
        return $this;
    }

    /**
     * Return the URL to display this. By default uses Http\SvcAssuranceDetails::class
     *
     * @param bool $refresh
     * @return string
     */
    public function getDetailsUrl(bool $refresh = false): string
    {
        $params = ["uuid" => $this->uuid];
        if ($refresh) {
            $params["refresh"] = "true";
        }
        return route("sadetails", $params);
    }
}
