<?php

namespace Support\Serializers\Fractal;

use League\Fractal\Serializer\DataArraySerializer;

class OptionalDataKeySerializer extends DataArraySerializer
{
    /**
     * @var bool
     */
    private $removeDataAttribute;

    /**
     * Create a new optional data key serializer.
     *
     * @param bool $removeDataAttribute
     */
    public function __construct(bool $removeDataAttribute = false)
    {
        $this->removeDataAttribute = $removeDataAttribute;
    }

    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        if ($this->removeDataAttribute) {
            return $data;
        }

        return ($resourceKey === false) ? $data : [$resourceKey ?: 'data' => $data];
    }

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        if ($this->removeDataAttribute) {
            return $data;
        }

        return ($resourceKey === false) ? $data : [$resourceKey ?: 'data' => $data];
    }
}
