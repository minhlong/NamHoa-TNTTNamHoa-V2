<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Không tìm thấy dữ liệu!',
            ], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Không tìm thấy trang!',
            ], 404);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'error' => $exception->errors(),
            ], 400);
        }

        if ($this->isHttpException($exception)) {
            switch ($exception->getStatusCode()) {
                case '403':
                    return response()->json([
                        'error' => 'Bạn chưa được phân quyền cho thao tác này!',
                    ], 403);
                    break;
                default:
                    break;
            }
        }

        return parent::render($request, $exception);
    }
}
