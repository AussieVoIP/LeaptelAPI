<?php

namespace Leaptel\Core;

use Leaptel\API\ServiceAssurance\ServiceAssuranceHistory;
use Illuminate\Database\Eloquent\Collection;
use Leaptel\API\Response\ServiceAssuranceResult;
use Leaptel\API\Response\ServiceAssuranceTestType;
use Leaptel\API\ServiceAssurance\RequestServiceAssuranceTest;
use Leaptel\API\ServiceAssurance\ServiceAssuranceTestTypes;
use Leaptel\Models\ServiceAssurance as ServiceAssuranceModel;

/** @package Leaptel\Core */
class ServiceAssuranceManager
{
    /**
     * @param string $service_id
     * @param bool $refresh
     * @return Collection<ServiceAssuranceResult>
     */
    public static function getAllServiceAssuranceTests(string $service_id, bool $refresh = false): Collection
    {
        $h = (new ServiceAssuranceHistory($service_id))->go($refresh);
        $c = new Collection();
        foreach ($h as $sar) {
            $m = ServiceAssuranceModel::getObject($service_id, $sar->test_id);
            $m->storeResult($sar);
            $c->add($m);
        }
        return $c;
    }

    /**
     * @param string $service_id
     * @param string $test_id
     * @param bool $refresh
     * @return ServiceAssuranceResult
     */
    public static function getServiceAssuranceTest(string $service_id, string $test_id, bool $refresh = false): ServiceAssuranceResult
    {
        $m = ServiceAssuranceModel::getObject($service_id, $test_id);
        return $m->getResult($refresh);
    }

    /**
     * @param null|string $onlytype Eg, 'FTTP' or 'HFC"
     * @param bool $refresh
     * @return \Leaptel\API\Response\ServiceAssuranceTestType[]
     */
    public static function getServiceAssuranceTestTypes(?string $onlytype = null, bool $refresh = false): array
    {
        $types = (new ServiceAssuranceTestTypes())->go($refresh, $onlytype);
        return $types;
    }

    public static function getServiceAssuranceTestType(string $access_tech, string $name): ServiceAssuranceTestType
    {
        $types = self::getServiceAssuranceTestTypes($access_tech);
        $key = $access_tech . "-" . $name;
        if (empty($types[$key])) {
            throw new \Exception("Could not find $key in test types");
        }
        return $types[$key];
    }

    /**
     * Send a request off to NBNco to actually run a test.
     *
     * @param ServiceAssuranceTestType $test
     * @param string $service_id
     * @return ServiceAssuranceModel
     */
    public static function requestServiceAssurance(ServiceAssuranceTestType $test, string $service_id): ServiceAssuranceModel
    {
        $r = (new RequestServiceAssuranceTest($service_id, $test))->go();
        return $r;
    }

    /**
     * Updates any ServiceAssurance requests that aren't complete. Returns a
     * collection of ones that STILL aren't complete.
     *
     *  @return Collection
     */
    public static function updateIncompleteTests(): Collection
    {
        $incomplete = new Collection();
        $tests = ServiceAssuranceModel::whereIn('request_status', ['requested', 'new', 'unknown'])->get();
        foreach ($tests as $t) {
            $t->updateResult();
            if (!$t->isComplete()) {
                $incomplete->add($t);
            }
        }
        return $incomplete;
    }
}
