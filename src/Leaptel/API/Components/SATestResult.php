<?php

namespace Leaptel\API\Components;

use Leaptel\API\Schemas\SchemaBase;

/**
 * @OA\Schema(description="NBNco Service Test EVENT remapped object", type="object")
 * @package Leaptel
 */
class SATestResult extends SchemaBase
{
    public array $__skipvars = [
        "serviceTest" => "Mostly redundant",
    ];

    /**
     * Huge amount of cleanups here. This looks like it originally was
     * an XML response
     *
     * @param array $row
     * @return void
     */
    public function finishImport(array $row)
    {
        $st = $row['serviceTest'] ?? [];
        $ref = $st['serviceRef']['id'] ?? null;
        if ($ref) {
            $this->serviceRef = $ref;
        }
        $testref = $st['testSpecificationRef']['id'] ?? null;
        if ($testref) {
            $this->testSpecificationRef = $testref;
        }
        $ed = $st['executionDate'] ?? [];
        $start = $ed['startDateTime'] ?? null;
        $end = $ed['endDateTime'] ?? null;
        if ($start) {
            $this->execStartTime = $start;
        }
        if ($end) {
            $this->execEndTime = $end;
        }
        $str = $st['serviceTestResults'][0] ?? [];
        $testtype = $str['type'] ?? null;
        if ($testtype) {
            $this->resultType = $testtype;
        }
        $measurements = $str['testMeasure'][0]['ntd']['measurements'] ?? [];
        foreach ($measurements as $m) {
            $mobj = new SAMeasurement($m);
            $this->measurements[$mobj->getKey()] = $mobj;
        }
    }

    /**
     * Event ID ('WRI60....')
     *
     * @var string
     * @OA\Property()
     */
    public string $id;

    /**
     * Type (Should be 'ServiceTestEvent')
     *
     * @var string
     * @OA\Property()
     */
    public string $objectType;

    /**
     * Appears to be a map back to the Leaptel Query
     *
     * @var string
     * @OA\Property()
     */
    public string $externalId;

    /**
     * Status ('Completed')
     *
     * @var string
     * @OA\Property()
     */
    public string $status;

    /**
     * Type of this notification ('TestCompleted')
     *
     * @var string
     * @OA\Property()
     */
    public string $notificationType;

    /**
     * Service Ref (from serviceTest.serviceRef.id)
     *
     * @var string
     * @OA\Property()
     */
    public string $serviceRef;

    /**
     * Test Specification (from serviceTest.testSpecificationRef.id)
     *
     * @var string
     * @OA\Property()
     */
    public string $testSpecificationRef;

    /**
     * Test Start (from serviceTest.executionDate.startDateTime)
     *
     * @var string
     * @OA\Property()
     */
    public string $execStartTime;

    /**
     * Test Finish (from serviceTest.executionDate.endDateTime)
     *
     * @var string
     * @OA\Property()
     */
    public string $execEndTime;

    /**
     * Test Type (from serviceTestResults[0].type)
     *
     * @var string
     * @OA\Property()
     */
    public string $resultType;

    /**
     * Test Measurements
     *
     * @var SAMeasurement[]
     * @OA\Property()
     */
    public array $measurements;
}
