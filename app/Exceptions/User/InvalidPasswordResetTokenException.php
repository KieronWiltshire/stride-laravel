<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class InvalidPasswordResetTokenException extends ValidationError
{ }
