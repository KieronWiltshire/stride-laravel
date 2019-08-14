<?php

namespace Support\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Support\Contracts\Repositories\AppRepository as AppRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AppRepository implements AppRepositoryInterface
{
    use UsesPagination, UsesRelationships, UsesOrderBy;

    /**
     * Execute the specified query including the specified
     * relations and pagination parameters.
     *
     * @param Builder $query
     * @return LengthAwarePaginator|Collection
     */
    protected function execute($query, $first = false)
    {
        if (isset($this->with) && count($this->with) > 0) {
            $query->with($this->relations);
        }

        if (isset($this->orderBy) && count($this->orderBy) > 0) {
            $column = $this->orderBy[0];
            $direction = isset($this->orderBy[1]) ? $this->orderBy[1] : null;

            $query->orderBy($column, $direction);
        }

        $result = null;

        if (!$first && $this->paginate) {
            if (isset($this->limit) && !is_null($this->limit)) {
                $result = $query->paginate($this->limit, ['*'], 'page', $this->offset);
            } else {
                $result = $query->get();
                $result = new LengthAwarePaginator($result->all(), $result->count(), max($result->count(), 1), $this->offset);
            }

            $result
                ->setPageName('offset')
                ->appends([
                    'limit' => request()->query('limit')
                ]);
        } else {
            if ($first) {
                $result = $query->first();
            } else {
                $result = $query->get();
            }
        }

        $this->relations = [];
        $this->orderBy = [];
        $this->paginate = false;

        return $result;
    }
}
