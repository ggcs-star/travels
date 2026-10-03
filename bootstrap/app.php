<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

// Ensure Laravel's Blade compiled-view directory exists before the application boots.
$compiledViewPath = dirname(__DIR__)
    . DIRECTORY_SEPARATOR . 'storage'
    . DIRECTORY_SEPARATOR . 'framework'
    . DIRECTORY_SEPARATOR . 'views';

if (! is_dir($compiledViewPath)) {
    @mkdir($compiledViewPath, 0775, true);
}

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Middleware Aliases
        |--------------------------------------------------------------------------
        */

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'api.token' => \App\Http\Middleware\ApiTokenAuth::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Global Middleware
        |--------------------------------------------------------------------------
        */

        $middleware->append(
            \App\Http\Middleware\ValidateConfiguredFileUploads::class
        );
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | API JSON Response
        |--------------------------------------------------------------------------
        */

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*')
                || $request->expectsJson(),
        );

        /*
        |--------------------------------------------------------------------------
        | Global Exception Handling
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            Throwable $exception,
            Request $request
        ) {

            /*
            |--------------------------------------------------------------------------
            | Check API Request
            |--------------------------------------------------------------------------
            */

            $isApi = $request->is('api/*')
                || $request->expectsJson();

            /*
            |--------------------------------------------------------------------------
            | API Errors
            |--------------------------------------------------------------------------
            |
            | Every API exception returns the same generic message.
            |
            | Important:
            | HTTP status code is preserved so frontend can still
            | differentiate between 401, 403, 404, 422, 405 and 500.
            |
            */

            if ($isApi) {

                $status = 500;

                /*
                |--------------------------------------------------------------------------
                | Validation Error
                |--------------------------------------------------------------------------
                */

                if ($exception instanceof ValidationException) {
                    $status = 422;
                }

                /*
                |--------------------------------------------------------------------------
                | Authentication Error
                |--------------------------------------------------------------------------
                */

                elseif ($exception instanceof AuthenticationException) {
                    $status = 401;
                }

                /*
                |--------------------------------------------------------------------------
                | Authorization Error
                |--------------------------------------------------------------------------
                */

                elseif ($exception instanceof AuthorizationException) {
                    $status = 403;
                }

                /*
                |--------------------------------------------------------------------------
                | Eloquent Model Not Found
                |--------------------------------------------------------------------------
                */

                elseif ($exception instanceof ModelNotFoundException) {
                    $status = 404;
                }

                /*
                |--------------------------------------------------------------------------
                | HTTP 404
                |--------------------------------------------------------------------------
                */

                elseif ($exception instanceof NotFoundHttpException) {
                    $status = 404;
                }

                /*
                |--------------------------------------------------------------------------
                | HTTP 405
                |--------------------------------------------------------------------------
                */

                elseif ($exception instanceof MethodNotAllowedHttpException) {
                    $status = 405;
                }

                /*
                |--------------------------------------------------------------------------
                | Other HTTP Exceptions
                |--------------------------------------------------------------------------
                */

                elseif ($exception instanceof HttpExceptionInterface) {
                    $status = $exception->getStatusCode();
                }

                /*
                |--------------------------------------------------------------------------
                | Final API Error Response
                |--------------------------------------------------------------------------
                */

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again later.',
                    'data' => null,
                    'errors' => [],
                ], $status);
            }

            /*
            |--------------------------------------------------------------------------
            | Normal Web Requests
            |--------------------------------------------------------------------------
            |
            | Keep normal Blade/browser exception handling unchanged.
            |
            */

            return null;
        });
    })

    ->create();