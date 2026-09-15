<?php

namespace App\Http\Middleware;

use App\Services\AllowedFileExtensionsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ValidateConfiguredFileUploads
{
    public function __construct(
        protected AllowedFileExtensionsService $extensions,
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $files = $request->allFiles();

        /*
         * No files = nothing to validate.
         */
        if ($files === []) {
            return $next($request);
        }

        try {
            /*
             * This is the central server-side gate.
             *
             * It applies to:
             * - Admin uploads
             * - User uploads
             * - API uploads
             * - Any other multipart upload
             */
            $this->extensions->validateFiles(
                $files
            );
        } catch (
            ValidationException $exception
        ) {
            /*
             * API response.
             */
            if (
                $request->is('api/*')
                || $request->expectsJson()
            ) {
                return response()->json(
                    [
                        'success' => false,

                        'message' =>
                            'The uploaded file type is not allowed.',

                        'errors' =>
                            $exception->errors(),
                    ],
                    422
                );
            }

            /*
             * Normal web request.
             *
             * Let Laravel's validation error handling
             * show the normal admin/user validation UI.
             */
            throw $exception;
        }

        return $next($request);
    }
}