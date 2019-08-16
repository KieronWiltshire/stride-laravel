<?php

namespace Support\Contracts\Repositories;

use Support\Exceptions\Pagination\InvalidPaginationException;
use Support\Repositories\UsesOrderBy;

interface AppRepository
{
    /**
     * Specify the repository to include relationships.
     *
     * @param array|string $relations
     * @return self
     */
    public function with($relations);

    /**
     * Specify the repository to order the response result by
     * the given parameters.
     *
     * @param array|string $orderBy
     * @return self
     */
    public function orderBy($orderBy = []);

    /**
     * Specify the repository to paginate the response.
     *
     * @param integer $limit
     * @param integer $offset
     * @return self
     *
     * @throws InvalidPaginationException
     */
    public function paginate($limit = null, $offset = 1);
}
