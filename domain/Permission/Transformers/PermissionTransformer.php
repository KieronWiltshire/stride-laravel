<?php

namespace Domain\Permission\Transformers;

use Domain\Permission\Permission;
use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{
  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform(Permission $permission)
  {
    $visible = [];

    return $permission->makeVisible($visible)->toArray();
  }
}