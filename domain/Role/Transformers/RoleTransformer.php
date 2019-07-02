<?php

namespace Domain\Role\Transformers;

use Domain\Role\Role;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform(Role $role)
  {
    $visible = [];

    return $role->makeVisible($visible)->toArray();
  }
}