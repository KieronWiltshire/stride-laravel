<?php

namespace App\Http;

use App\Http\Middleware\AssignDefaultRole;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckForMaintenanceMode;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\PrettyPrint;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VersionHeader;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;
use Laravel\Passport\Http\Middleware\CheckScopes;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
    CheckForMaintenanceMode::class,
    ValidatePostSize::class,
    TrimStrings::class,
    ConvertEmptyStringsToNull::class,
    TrustProxies::class,
    PrettyPrint::class,
    VersionHeader::class,
    AssignDefaultRole::class,
  ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
    'web' => [
      EncryptCookies::class,
      AddQueuedCookiesToResponse::class,
      StartSession::class,
      AuthenticateSession::class,
      ShareErrorsFromSession::class,
//      \App\Http\Middleware\VerifyCsrfToken::class, // This isn't needed because the application uses stateless tokens for authentication
      SubstituteBindings::class,
    ],

    'api' => [
      'throttle:60,1',
      'bindings',
    ],

    /**
     * @notice
     *
     * The OAuth middleware group requires a session in order to store
     * the authorization request, however these is no need to protect
     * against CSRF attacks because the authentication process is handled
     * in a stateless manner. If the authenticated user does not match the
     * authorization request, then OAuth fails.
     */
    'oauth' => [
      EncryptCookies::class,
      AddQueuedCookiesToResponse::class,
      StartSession::class,
      'throttle:60,1',
      'bindings',
    ],
  ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
    'auth' => Authenticate::class,
    'auth.basic' => AuthenticateWithBasicAuth::class,
    'bindings' => SubstituteBindings::class,
    'cache.headers' => SetCacheHeaders::class,
    'can' => Authorize::class,
    'guest' => RedirectIfAuthenticated::class,
    'signed' => ValidateSignature::class,
    'throttle' => ThrottleRequests::class,
    'verified' => EnsureEmailIsVerified::class,
    'scopes' => CheckScopes::class,
    'scope' => CheckForAnyScope::class,
    'client' => CheckClientCredentials::class,
  ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
    StartSession::class,
    ShareErrorsFromSession::class,
    Authenticate::class,
    AuthenticateSession::class,
    SubstituteBindings::class,
    Authorize::class,
  ];
}
