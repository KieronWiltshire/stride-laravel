<?php

namespace App\Exceptions\User;

use App\Exceptions\Http\ValidationError;

class InvalidEmailVerificationTokenException extends ValidationError
{ }
