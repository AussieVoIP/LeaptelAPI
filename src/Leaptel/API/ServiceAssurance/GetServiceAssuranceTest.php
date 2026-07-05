<?php

namespace Leaptel\API\ServiceAssurance;

use Leaptel\API\APIBase;
use Leaptel\API\Response\ServiceAssuranceResult;

/**
 * You should be using Core\ServiceAssuranceManager to interact with this API, via the
 * ServiceAssurance model.
 *
 * @package Leaptel\API\ServiceAssurance
 */
class GetServiceAssuranceTest extends APIBase
{
    protected string $retclass = ServiceAssuranceResult::class;
    protected int $cacheforsecs = 300;

    /**
     * @param string $service_id
     * @param string $test_id
     * @return void
     */
    public function __construct(
        private string $service_id,
        private string $test_id
    ) {
        $this->path = '/services/' . $this->service_id . '/assurance-tests/' . $this->test_id;
    }

    /**
     * @param bool $refresh
     */
    public function go(bool $refresh = false): ServiceAssuranceResult
    {
        $s = $this->getSingle($refresh);
        return $s;
    }
}
