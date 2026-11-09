<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Saat APP_DEBUG=true tampilkan error bawaan Lumen (untuk debugging)
        if (env('APP_DEBUG')) {
            return parent::render($request, $exception);
        }

        $status = 500;
        $message = 'HTTP_INTERNAL_SERVER_ERROR';

        if ($exception instanceof MethodNotAllowedHttpException) {
            $status = 405;
            $message = 'HTTP_METHOD_NOT_ALLOWED';
        } elseif ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
            $status = 404;
            $message = 'HTTP_NOT_FOUND';
        } elseif ($exception instanceof AuthorizationException) {
            $status = 403;
            $message = 'HTTP_FORBIDDEN';
        } elseif ($exception instanceof ValidationException) {
            $status = 400;
            $message = 'HTTP_BAD_REQUEST';
        } elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            $message = 'HTTP_ERROR';
        }

        return response()->json([
            'success' => false,
            'status'  => $status,
            'message' => $message,
        ], $status);
    }
    

}
