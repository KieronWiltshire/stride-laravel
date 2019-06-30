<?php

namespace App\User;

use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Authenticatable
{
  use HasApiTokens, Notifiable, LaratrustUserTrait, Authorizable {
    Authorizable::can insteadof LaratrustUserTrait;
    LaratrustUserTrait::can as laratrustCan;
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'email',
    'email_verified_at',
    'email_verification_token',
    'password',
    'password_reset_token'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'email',
    'password',
    'email_verification_token',
    'password_reset_token'
  ];

  /**
   * The attributes that should be mutated to dates.
   *
   * @var array
   */
  protected $dates = [
    'email_verified_at',
  ];

  /**
   * Set the user's password.
   *
   * @param string $password
   * @return void
   */
  public function setPasswordAttribute($password)
  {
    $this->attributes['password'] = Hash::make($password);
  }
}
