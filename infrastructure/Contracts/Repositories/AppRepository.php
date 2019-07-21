<?php

namespace Infrastructure\Contracts\Repositories;

interface AppRepository
{
  /**
   * Specify the repository to include relationships.
   *
   * @param array|string $relations
   * @return self
   */
  function with($relations);

  /**
   * Specify the repository to paginate the response.
   *
   * @param integer $limit
   * @param integer $offset
   * @return self
   *
   * @throws \Infrastructure\Exceptions\Pagination\InvalidPaginationException
   */
  function paginate($limit = null, $offset = 1);
}