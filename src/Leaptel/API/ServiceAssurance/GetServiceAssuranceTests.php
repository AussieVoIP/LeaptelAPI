<?php

namespace Leaptel\API\ServiceAssurance;

use Leaptel\API\APIBase;
use Leaptel\API\Response\ServiceAssuranceTest;

class GetServiceAssuranceTests extends APIBase
{
    protected string $path = '/service-assurance-tests';
    protected string $query = '?carrier=nbn&access_technology=FW';
    protected string $retclass = ServiceAssuranceTest::class;
    protected string $indexby = "abbreviation";
    protected int $cacheforsecs = 86400;

    /**
     * @param bool $refresh
     * @return ServiceAssuranceTest[]
     */
    public function go(bool $refresh = false)
    {
        return $this->getMultipleNotPaginated($refresh);
    }
}
