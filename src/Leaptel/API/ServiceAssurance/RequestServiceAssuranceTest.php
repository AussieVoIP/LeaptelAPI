<?php

namespace Leaptel\API\ServiceAssurance;

use Leaptel\API\APIBase;
use Leaptel\API\Response\ServiceAssuranceResult;
use Leaptel\API\Response\ServiceAssuranceTestType;
use Leaptel\Models\ServiceAssurance;

/**
 * You should be using Core\ServiceAssuranceManager to interact with this API, via the
 * ServiceAssurance model.
 *
 * @package Leaptel\API\ServiceAssurance
 */
class RequestServiceAssuranceTest extends APIBase
{
    protected string $retclass = ServiceAssuranceResult::class;

    /**
     * @param string $service_id
     * @param ServiceAssuranceTestType $test_type
     * @return void
     */
    public function __construct(
        private string $service_id,
        private ServiceAssuranceTestType $test_type
    ) {
        $this->path = '/services/' . $this->service_id . '/assurance-tests';
    }

    public function getFormParams(): array
    {
        return [
            "test_number" => $this->test_type->test_id,
        ];
    }

    public function go(): ServiceAssurance
    {
        $res = $this->postEvent();
        $model = ServiceAssurance::fromResult($res);
        return $model;
    }
}
