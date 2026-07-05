<?php

namespace Leaptel\API\ServiceAssurance;

use Leaptel\API\APIBase;
use Leaptel\API\Response\ServiceAssuranceTestType;

/**
 * You should be using Core\ServiceAssuranceManager to interact with this API
 *
 * @package Leaptel\API\ServiceAssurance
 */
class ServiceAssuranceTestTypes extends APIBase
{
    protected string $path = '/service-assurance-tests';
    protected string $retclass = ServiceAssuranceTestType::class;
    protected int $cacheforsecs = 2592000; // 30 Days. This shouldn't change that much!
    protected bool $add_resp_id = true;

    /**
     * @param bool $refresh
     * @param null|string $onlytype Only return this type of Tests
     * @return ServiceAssuranceTestType[]
     */
    public function go(bool $refresh = false, ?string $onlytype = null)
    {
        $retarr = [];
        $types = ["FTTN", "FTTC", "FTTP", "FW", "HFC"]; // Unknown type for Satellite
        $results = [];
        foreach ($types as $t) {
            if ($onlytype !== null && $onlytype !== $t) {
                continue;
            }
            $results[$t] = $this->getTestType($t, $refresh);
        }
        foreach ($results as $type => $objs) {
            foreach ($objs as $o) {
                $o->techtype = $type;
                $name = $type . "-" . $o->abbreviation;
                $retarr[$name] = $o;
            }
        }
        return $retarr;
    }

    public function getTestType(string $tech, bool $refresh = false)
    {
        $this->query = '?carrier=nbn&access_technology=' . $tech;
        $objs =  $this->getMultipleNotPaginated($refresh);
        return $objs;
    }
}
