<?php

namespace Support\Repositories;

trait UsesOrderBy
{
    /**
     * @var array
     */
    protected $orderBy = [];

    /**
     * Specify the repository to order the response result by
     * the given parameters.
     *
     * @param array|string $orderBy
     * @return self
     */
    public function orderBy($orderBy = [])
    {
        if (!is_array($orderBy) && is_string($orderBy)) {
            $orderBy = [$orderBy, 'asc'];
        }

        $this->orderBy = $orderBy;

        return $this;
    }
}
