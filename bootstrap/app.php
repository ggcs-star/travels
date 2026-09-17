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
// Do not call Laravel helpers such as storage_path() here: helpers are not
// guaranteed to be available before the Application has been configured.
$compiledViewPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views';
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
        | Admin Middleware
        |--------------------------------------------------------------------------
        */

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'api.token' => \App\Http\Middleware\ApiTokenAuth::class,
        ]);

        $middleware->append(
            \App\Http\Middleware\ValidateConfiguredFileUploads::class
        );

    })

    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*')
                || $request->expectsJson(),
        );

        /*
        |--------------------------------------------------------------------------
        | Safe production-style exception responses
        |--------------------------------------------------------------------------
        |
        | User-facing pages never expose stack traces, file paths, SQL errors,
        | credentials, or exception messages. API clients receive a stable
        | JSON error contract instead.
        |
        */
        $exceptions->render(function (Throwable $exception, Request $request) {

            $isApi = $request->is('api/*') || $request->expectsJson();

            if ($isApi) {
                if ($exception instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please check the submitted information.',
                        'errors' => $exception->errors(),
                    ], 422);
                }

                if ($exception instanceof AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Authentication is required.',
                        'errors' => [],
                    ], 401);
                }

                if ($exception instanceof AuthorizationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have permission to perform this action.',
                        'errors' => [],
                    ], 403);
                }

                $status = 500;

                if ($exception instanceof ModelNotFoundException
                    || $exception instanceof NotFoundHttpException) {
                    $status = 404;
                } elseif ($exception instanceof MethodNotAllowedHttpException) {
                    $status = 405;
                } elseif ($exception instanceof HttpExceptionInterface) {
                    $status = $exception->getStatusCode();
                }

                if ($status < 400 || $status > 599) {
                    $status = 500;
                }

                $messages = [
                    400 => 'The request could not be processed.',
                    401 => 'Authentication is required.',
                    403 => 'You do not have permission to perform this action.',
                    404 => 'The requested resource was not found.',
                    405 => 'This action is not available.',
                    408 => 'The request timed out.',
                    409 => 'The request conflicts with the current state.',
                    419 => 'The request has expired. Please try again.',
                    422 => 'Please check the submitted information.',
                    429 => 'Too many requests. Please try again later.',
                    500 => 'An unexpected server error occurred.',
                    502 => 'The service is temporarily unavailable.',
                    503 => 'The service is temporarily unavailable.',
                    504 => 'The service took too long to respond.',
                ];

                return response()->json([
                    'success' => false,
                    'message' => $messages[$status] ?? 'An unexpected error occurred.',
                    'errors' => [],
                ], $status);
            }

            /*
             * Keep normal browser validation/authentication flows intact.
             */
            if ($exception instanceof ValidationException
                || $exception instanceof AuthenticationException) {
                return null;
            }

            $status = 500;

            if ($exception instanceof ModelNotFoundException
                || $exception instanceof NotFoundHttpException) {
                $status = 404;
            } elseif ($exception instanceof AuthorizationException) {
                $status = 403;
            } elseif ($exception instanceof MethodNotAllowedHttpException) {
                $status = 405;
            } elseif ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
            }

            if ($status < 400 || $status > 599) {
                $status = 500;
            }

            $view = view()->exists('errors.' . $status)
                ? 'errors.' . $status
                : 'errors.500';

            $messages = [
                400 => 'Bad request',
                401 => 'Sign-in required',
                403 => 'Access denied',
                404 => 'Page not found',
                405 => 'Action not allowed',
                408 => 'Request timed out',
                409 => 'Request conflict',
                419 => 'Page expired',
                422 => 'Unable to process',
                429 => 'Too many requests',
                500 => 'Something went wrong',
                502 => 'Service unavailable',
                503 => 'Temporarily unavailable',
                504 => 'Request timed out',
            ];

            return response()->view(
                $view,
                [
                    'code' => $status,
                    'title' => $messages[$status] ?? 'Something went wrong',
                    'message' => $messages[$status] ?? 'Please try again later.',
                ],
                $status
            );
        });

    })

    ->create();