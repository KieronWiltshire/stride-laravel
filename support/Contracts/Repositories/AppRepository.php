<?php

namespace Support\Contracts\Repositories;

use Support\Repositories\UsesOrderBy;

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
   * Specify the repository to order the response result by
   * the given parameters.
   *
   * @param array|string $orderBy
   * @return self
   */
  function orderBy($orderBy = []);

  /**
   * Specify the repository to paginate the response.
   *
   * @param integer $limit
   * @param integer $offset
   * @return self
   *
   * @throws \Support\Exceptions\Pagination\InvalidPaginationException
   */
  function paginate($limit = null, $offset = 1);
}
