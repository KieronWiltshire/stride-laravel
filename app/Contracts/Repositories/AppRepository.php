<?php

namespace App\Contracts\Repositories;

interface AppRepository
{
  /**
   * Specify the repository to include relationships.
   *
   * @param array|string $relations
   * @return \App\Contracts\Repositories\AppRepository
   */
  function with($relations);

  /**
   * Specify the repository to paginate the response.
   *
   * @param integer $limit
   * @param integer $offset
   * @return \App\Contracts\Repositories\AppRepository
   *
   * @throws \App\Exceptions\Pagination\InvalidPaginationException
   */
  function paginate($limit = null, $offset = 1);
}