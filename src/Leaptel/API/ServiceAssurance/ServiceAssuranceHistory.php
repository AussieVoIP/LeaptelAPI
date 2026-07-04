<?php

namespace Leaptel\API\ServiceAssurance;

use Leaptel\API\APIBase;
use Leaptel\API\Response\GenericResponse;
use Leaptel\API\Response\ServiceAssuranceResult;
use Leaptel\API\Response\ServiceAssuranceTestType;

class ServiceAssuranceHistory extends APIBase
{
    protected string $retclass = ServiceAssuranceResult::class;
    protected int $cacheforsecs = 86400;

    private string $serviceid;

    /**
     * @param string $serviceid
     * @return void
     */
    public function __construct(string $serviceid)
    {
        $this->serviceid = $serviceid;
        $this->path = '/services/' . $this->serviceid . '/assurance-tests-history';
    }

    /**
     * @param bool $refresh
     */
    public function go(bool $refresh = false)
    {
        return $this->getMultipleNotPaginated($refresh);
    }
}
