<?php

namespace Domain\Role;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'pivot'
  ];
}