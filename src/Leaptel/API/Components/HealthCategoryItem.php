<?php

namespace Leaptel\API\Components;

use Leaptel\API\Schemas\SchemaBase;

/**
 * @OA\Schema(description="Part of the standard Service Health SA response", type="object")
 * @package Leaptel
 */
class HealthCategoryItem extends SchemaBase
{
    /**
     * Remap @type to type_attribute
     *
     * @param array $row
     * @return void
     */
    public function __construct(array $row)
    {
        $row['type_attribute'] = $row['@type'];
        unset($row['@type']);
        return parent::__construct($row);
    }

    public function getKey(): string
    {
        return trim(str_replace(" ", "_", $this->id));
    }

    /**
     * ID (Name of the item)
     *
     * @var string
     * @OA\Property()
     */
    public string $id;

    /**
     * Type (from the category)
     *
     * @var string
     * @OA\Property()
     */
    public string $type;

    /**
     * Type Attribute (from @type)
     *
     * @var string
     * @OA\Property()
     */
    public string $type_attribute;

    /**
     * Status
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $status;

    /**
     * Time Stamp, in "2026-07-05T04:04:46.698Z" format
     *
     * @var string
     * @OA\Property()
     */
    public string $timeStamp;

    /**
     * Unit
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $unit;

    /**
     * Value
     *
     * @var null|string
     * @OA\Property()
     */
    public ?string $value;
}
