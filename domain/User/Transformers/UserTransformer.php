<?php

namespace Domain\User\Transformers;

use Domain\User\User;
use Illuminate\Support\Facades\Gate;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform(User $user)
  {
    $visible = [];

    $viewUserDetail = Gate::allows('user.view', $user);

    if ($viewUserDetail || request()->route()->hasParameter('email')) {
      $visible[] = 'email';
    }

    return $user->makeVisible($visible)->toArray();
  }
}