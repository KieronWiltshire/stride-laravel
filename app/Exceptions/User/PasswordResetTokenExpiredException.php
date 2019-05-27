<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class PasswordResetTokenExpiredException extends ValidationError
{ }
