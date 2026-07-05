<?php

namespace Leaptel\API\Components;

use Leaptel\API\Schemas\SchemaBase;

/**
 * TODO: Finish all the OA attributes for these things. Sigh.
 *
 * @OA\Schema(description="Returned Part of the Service Health SA", type="object")
 * @package Leaptel
 */
class ServiceHealth extends SchemaBase
{
    /**
     * Don't import the healthCategory array as it's useless. We
     * flatten it here into useful chunks.
     *
     * @param array $row
     * @return void
     */
    public function __construct(array $row)
    {
        $hc = $row['healthCategory'] ?? [];
        unset($row['healthCategory']);
        foreach ($hc as $section) {
            $type = $section['type'];
            $this->healthItems[$type] = [];
            foreach ($section['healthCategoryItem'] as $i) {
                $hci = new HealthCategoryItem($i);
                $hci->type = $type;
                $this->healthItems[$type][$hci->getKey()] = $hci;
            }
        }
        return parent::__construct($row);
    }

    /**
     * Status
     *
     * @var string
     * @OA\Property()
     */
    public string $status;

    public string $externalId;

    public string $id;

    public string $avcId;

    public string $href;

    public array $currentCondition;

    public array $overviewIndicator;

    public array $serviceHealthSpecification;

    public array $healthItems;
}
