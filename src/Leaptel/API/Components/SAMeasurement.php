<?php

namespace Leaptel\API\Components;

use Leaptel\API\Schemas\SchemaBase;

/**
 * @OA\Schema(description="NBNco Service Test EVENT remapped object", type="object")
 * @package Leaptel
 */
class SAMeasurement extends SchemaBase
{
    /**
     * This has '@type', 'id' and 'value'. If @type is not 'Indicator', it crashes.
     *
     * @param array $row
     * @return void
     */
    public function __construct(array $row)
    {
        $type = $row['@type'];
        if ($type !== "Indicator") {
            throw new \Exception("Unknown type $type, bug");
        }
        $this->id = $row['id'];
        $this->value = $row['value'];
    }

    public function getKey(): string
    {
        return trim(str_replace(" ", "_", $this->id));
    }

    /**
     * ID (Name of the measurement)
     *
     * @var string
     * @OA\Property()
     */
    public string $id;

    /**
     * Value of the measurement
     *
     * @var string
     * @OA\Property()
     */
    public string $value;
}
