<?php

namespace App\Entities;

use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'email',
    'email_verified',
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
    'password',
    'email_verification_token',
    'password_reset_token'
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified' => 'boolean',
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
