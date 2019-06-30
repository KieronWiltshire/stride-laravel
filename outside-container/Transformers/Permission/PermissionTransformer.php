<?php

namespace App\Transformers\Permission;

use App\Entities\Permission;
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