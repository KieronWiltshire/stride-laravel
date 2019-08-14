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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the role's is default attribute.
     *
     * @param string $isDefault
     * @return string
     */
    public function getIsDefaultAttribute($isDefault)
    {
        return filter_var($isDefault, FILTER_VALIDATE_BOOLEAN);
    }
}
