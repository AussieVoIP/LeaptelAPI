<?php

namespace Leaptel\API\ServiceAssurance;

use Leaptel\API\APIBase;
use Leaptel\API\Response\ServiceAssuranceTest;

class GetServiceAssuranceTests extends APIBase
{
    protected string $path = '/service-assurance-tests';
    protected string $query = '?carrier=nbn&access_technology=ERROR';
    protected string $retclass = ServiceAssuranceTest::class;
    protected string $indexby = "abbreviation";
    protected int $cacheforsecs = 86400;

    /**
     * @param bool $refresh
     * @return ServiceAssuranceTest[]
     */
    public function go(bool $refresh = false)
    {
        $retarr = [];
        $results = [
            "FTTN" => $this->getTestType("FTTN", $refresh),
            "FTTC" => $this->getTestType("FTTC", $refresh),
            "FTTP" => $this->getTestType("FTTP", $refresh),
            "FW" => $this->getTestType("FW", $refresh),
            "HFC" => $this->getTestType("HFC", $refresh),
            "SATELLITE" => $this->getTestType("SATELLITE", $refresh),
        ];
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
