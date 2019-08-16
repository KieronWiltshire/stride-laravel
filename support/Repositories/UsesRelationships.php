<?php


namespace Support\Repositories;

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
     * @return self
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
