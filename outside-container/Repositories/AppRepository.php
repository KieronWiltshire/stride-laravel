<?php

namespace App\Repositories;

use App\Contracts\Repositories\AppRepository as AppRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AppRepository implements AppRepositoryInterface
{
  use UsesPagination, UsesRelationships;

  /**
   * Execute the specified query including the specified
   * relations and pagination parameters.
   *
   * @param \Illuminate\Database\Query\Builder $query
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
   */
  protected function execute($query, $first = false)
  {
    if (isset($this->with) && count($this->with) > 0) {
      $query->with($this->relations);
    }

    $result = null;

    if (!$first && isset($this->paginate)) {
      if (isset($limit) && !is_null($limit)) {
        $result = $query->paginate($this->limit, ['*'], 'page', $this->offset);
      } else {
        $result = $query->get();
        $result = new LengthAwarePaginator($result->all(), $result->count(), max($result->count(), 1), 1);
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

    $this->with([]);
    $this->paginate = false;

    return $result;
  }
}