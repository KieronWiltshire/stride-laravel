<?php

return [
  /*
   * Role permission structure
   */
  'role_structure' => [
    'administrator' => [
      'user' => '*',
      'role' => '*',
      'permission' => '*',
      'client' => '*',
      'personal-access-token' => '*'
    ],
    'user' => [
      'user' => 'view-me,update-me',
      'client' => 'create,view-me,update-me,delete-me',
      'personal-access-token' => 'create,view-me,delete-me',
    ]
  ],

  /**
   * User permission structure.
   */
  'permission_structure' => [],

  /**
   * Permission mapping
   */
  'permissions_map' => [
    '*' => '*',
    'create' => 'create',
    'view-all' => 'view.all',
    'view-me' => 'view.me',
    'update-me' => 'update.me',
    'update-all' => 'update.all',
    'delete-me' => 'delete.me',
    'delete-all' => 'delete.all',
    'assign-all' => 'assign.all',
    'deny-all' => 'deny.all',
    'assign-role' => 'assign-role',
    'deny-role' => 'deny-role',
    'assign-permission' => 'assign-permission',
    'deny-permission' => 'deny-permission'
  ]
];
