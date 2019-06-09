<?php

return [
  'role_structure' => [
    'administrator' => [
      'users' => 'c,r,u,d',
    ],
    'user' => [
      'users' => 'c,r,u'
    ],
    'guest' => [
      'users' => 'c,r'
    ]
  ],
  'permission_structure' => [],
  'permissions_map' => [
    'c' => 'create',
    'r' => 'read',
    'u' => 'update',
    'd' => 'delete',
  ]
];
