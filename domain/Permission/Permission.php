<?php

namespace Domain\Permission;

use Laratrust\Models\LaratrustPermission;

class Permission extends LaratrustPermission
{
  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'pivot'
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'is_default' => 'boolean',
  ];
}