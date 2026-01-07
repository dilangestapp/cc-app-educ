<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\EnsureRole;

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| ✅ Middleware alias (dans bootstrap, compatible Laravel 10)
|--------------------------------------------------------------------------
| On déclare l'alias 'role' ici, pour que tes routes puissent faire:
| ->middleware('role:admin')
*/
$app->booted(function () use ($app) {
    $app['router']->aliasMiddleware('role', EnsureRole::class);
});

return $app;
