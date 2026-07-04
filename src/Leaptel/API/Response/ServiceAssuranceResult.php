<?php

namespace Leaptel\API\Response;

use Leaptel\API\Components\SATestResult;
use Leaptel\API\Schemas\ResponseBase;

/**
 * @OA\Schema(description="From Service Assurance History", type="object")
 * @package Leaptel
 */
class ServiceAssuranceResult extends ResponseBase
{
    public array $__mappings = [
        "raw_test_result" => "test_result",
    ];

    public array $__skipvars = [
        "test_result" => "Is imported as part of finishImport",
    ];

    public function finishImport(array $row)
    {
        $r = $row['test_result'] ?? null;
        if ($r) {
            $res = json_decode($r, true);
            if (!is_array($res)) {
                $this->test_error = $r;
            } else {
                $this->test_result  = new SATestResult($res);
            }
        }
    }

    /**
     * Test ID
     *
     * @var int
     * @OA\Property()
     */
    public int $test_id;

    /**
     * Test Name
     *
     * @var string
     * @OA\Property()
     */
    public string $test_name;

    /**
     * Service ID
     *
     * @var int
     * @OA\Property()
     */
    public int $service_id;

    /**
     * Type ID
     *
     * @var int
     * @OA\Property()
     */
    public int $type_id;

    /**
     * Provider
     *
     * @var string
     * @OA\Property()
     */
    public string $provider;

    /**
     * Test Number
     *
     * @var int
     * @OA\Property()
     */
    public int $test_number;

    /**
     * Test Status
     *
     * @var string
     * @OA\Property()
     */
    public string $request_status;

    /**
     * Test Result Object
     *
     * @var null|SATestResult
     * @OA\Property()
     */
    public ?SATestResult $test_result = null;

    /**
     * Test Error string from test_result
     *
     * @var null|SATestResult
     * @OA\Property()
     */
    public string $test_error;

    /**
     * Requested at (Note is 'requested_dt')
     *
     * @var string
     * @OA\Property()
     */
    public string $requested_dt;

    /**
     * Submitted at (Note is 'submitted_dt')
     *
     * @var string
     * @OA\Property()
     */
    public string $submitted_dt;

    /**
     * Completed at (Note is 'completed_dt')
     *
     * @var string
     * @OA\Property()
     */
    public string $completed_dt;

    /**
     * Raw Test Result (JSON) - Remapped from test_result
     *
     * @var string
     * @OA\Property()
     */
    public string $raw_test_result;

    /**
     * Service Type (eg 'FTTP')
     *
     * @var string
     * @OA\Property()
     */
    public string $serviceType;

    /**
     * Access Type (eg 'NFAS')
     *
     * @var string
     * @OA\Property()
     */
    public string $accessType;

    /**
     * Speed ('25Mbps/5Mbps')
     *
     * @var string
     * @OA\Property()
     */
    public string $serviceSpeed;

    /**
     * Physical Address ('LOT 1 123 SMITH ST BRISBANE QLD 4000 ') - Warning: I have
     * noticed it has a trailing space.
     *
     * @var string
     * @OA\Property()
     */
    public string $address;

    /**
     * NBN Locality ('BRISBANE')
     *
     * @var string
     * @OA\Property()
     */
    public string $locality;

    /**
     * Postcode ('4000')
     *
     * @var string
     * @OA\Property()
     */
    public string $postcode;

    /**
     * Group ID - don't know what this is for, empty string so far.
     *
     * @var string
     * @OA\Property()
     */
    public string $group_id;

    /**
     * Service Qualification result? Empty string so far.
     *
     * @var string
     * @OA\Property()
     */
    public string $service_qual;
}
