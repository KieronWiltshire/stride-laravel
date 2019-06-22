<?php

namespace App\Transformers;

use App\Entities\Role;
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