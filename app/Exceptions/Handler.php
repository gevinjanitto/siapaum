<?php

namespace App\Exceptions;

use Throwable;
use App\Helpers\ResponseFormatter;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return ResponseFormatter::error([
                    'message' => 'Valid api key required'
                ], 'Unauthorized', 401);
            }
        });

        // response internal server error for api
        $this->renderable(function (Throwable $e, $request) {
            // only handle internal server error for api not validation error
            if($e instanceof \Illuminate\Validation\ValidationException) {
                return;
            }

            if($request->is('api/*')) {
                // rate limiting error
                if($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                    return ResponseFormatter::error([
                        'message' => 'Too many requests. Please try again later. Maximum 60 request per minute.'
                    ], 'Too Many Requests', 429);
                }
            }

            if ($request->is('api/*')) {
                return ResponseFormatter::error([
                    'message' => 'Internal server error'
                ], 'Internal Server Error', 500);
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
