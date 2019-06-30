<?php


namespace App\Repositories;


trait UsesRelationships
{
  /**
   * @var array
   */
  protected $relations = [];

  /**
   * Specify the repository to include relationships.
   *
   * @param array|string $relations
   * @return \App\Contracts\Repositories\AppRepository
   */
  public function with($relations)
  {
    if (is_string($relations)) {
      $this->relations = explode(',', $relations);
    } else {
      $this->relations = is_array($relations) ? $relations : [];
    }

    return $this;
  }
}