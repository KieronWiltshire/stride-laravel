<?php

namespace Infrastructure\Serializers\Fractal;

use League\Fractal\Serializer\DataArraySerializer;

class OptionalDataKeySerializer extends DataArraySerializer
{
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
    return ($resourceKey === false) ? $data : [$resourceKey ?: 'data' => $data];
  }
}