<?php


namespace Support\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;

trait UsesPagination
{
    /**
     * @var boolean
     */
    protected $paginate = false;

    /**
     * @var integer
     */
    protected $limit = null;

    /**
     * @var integer
     */
    protected $offset = 1;

    /**
     * Specify the repository to paginate the response.
     *
     * @param integer $limit
     * @param integer $offset
     * @return self
     *
     * @throws BindingResolutionException
     */
    public function paginate($limit = null, $offset = 1)
    {
        $offset = ($offset + 1);

        $validator = app()->make('Support\Validators\Pagination\PaginationValidator');

        $validator->validate([
            'limit' => $limit,
            'offset' => $offset
        ]);

        $this->paginate = true;
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }
}
