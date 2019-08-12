<?php

namespace Domain\Menu;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [];

  /**
   * The attributes that should be mutated to dates.
   *
   * @var array
   */
  protected $dates = [];

  /**
   * Retrieve the items associated to the menu.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function items()
  {
    return $this->hasMany('Domain\Menu\Item');
  }
}
