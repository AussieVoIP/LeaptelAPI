<?php

namespace Leaptel\API\Schemas;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

abstract class SchemaBase implements
    Arrayable,
    ArrayAccess,
    Jsonable,
    JsonSerializable
{
    public array $__mappings = [];
    public array $__skipvars = [];
    public array $__importvars = [];

    protected ?array $__original = null;

    /** @return void  */
    public function __construct(array $row = [])
    {
        // Preserve what we were originally given
        // $this->__original = $row;

        // If there's no mappings, just load it straight up
        if (empty($this->__mappings)) {
            foreach ($row as $key => $value) {
                // As long as we haven't been told to skip it, set it.
                if (empty($this->__skipvars[$key])) {
                    $this->{$key} = $value;
                }
            }
        } else {
            // print "Mappings found!\n";
            // Keep a record of which settings have already been imported
            $processed = [];
            foreach ($this->__mappings as $dest => $src) {
                if (is_array($src)) {
                    $tmparr = [];
                    foreach ($src as $k) {
                        $tmparr[] = $row[$k];
                    }
                    $content = join(" ", $tmparr);
                } else {
                    $content = $row[$src];
                }
                $processed[$dest] = true;
                $this->{$dest} = $content;
            }
            // Now, do we have a list of things to only import?
            if (!empty($this->__importvars)) {
                print "Importvars provided\n";
                exit;
            } else {
                // Iterate through everything else in row, and see if we have a definition for it
                foreach ($row as $key => $value) {
                    if (empty($this->__skipvars[$key])) {
                        if (property_exists($this, $key)) {
                            if (isset($processed[$key])) {
                                throw new \Exception("Trying to import already imported $key, bug");
                            }
                            $this->{$key} = $value;
                            $processed[$key] = true;
                        }
                    }
                }
            }
            if (method_exists($this, "finishImport")) {
                $this->finishImport($row);
            }
        }
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->{$key})) {
            return $this->{$key};
        }
    }

    /**
     * @param mixed $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->{$key});
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->{$key} = $value;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->{$offset} = $value;
    }

    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->{$offset});
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->{$offset});
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return isset($this->{$offset}) ? $this->{$offset} : null;
    }

    /** @return mixed  */
    public function jsonSerialize(): array
    {
        return $this->getCollection()->toArray();
    }

    /**
     * @param integer $options
     * @return string
     */
    public function toJson($options = 0) // phpcs:ignore Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
    {
        return $this->getCollection()->toJson($options);
    }

    /** @return array  */
    public function toArray()
    {
        return $this->getCollection()->toArray();
    }

    /** @return \Illuminate\Support\Collection  */
    public function getCollection(): \Illuminate\Support\Collection
    {
        $props = get_object_vars($this);
        $collection = collect();
        foreach ($props as $key => $value) {
            // Ignore anything starting with two underscores
            if (strpos($key, "__") !== 0) {
                $collection->put($key, $value);
            }
        }
        return $collection;
    }
}
