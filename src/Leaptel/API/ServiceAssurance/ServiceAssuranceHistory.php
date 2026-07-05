<?php

namespace Leaptel\API\ServiceAssurance;

use Leaptel\API\APIBase;
use Leaptel\API\Response\ServiceAssuranceResult;

/**
 * You should be using Core\ServiceAssuranceManager to interact with this API
 *
 * @package Leaptel\API\ServiceAssurance
 */
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
     * Note that this actually IS PAGINATED, according to the docs, but
     * there's nothing in the result that says how many pages they are. So,
     * we just don't ask for ANY pages, which will return page 1, which has
     * a maximum of 20 results.
     *
     * @param bool $refresh
     */
    public function go(bool $refresh = false)
    {
        return $this->getMultipleNotPaginated($refresh);
    }
}
