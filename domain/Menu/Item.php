<?php

namespace Domain\Menu;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
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
   * Retrieve the item type.
   *
   * @return mixed
   */
  public function type()
  {
    return $this->morphTo();
  }
}
