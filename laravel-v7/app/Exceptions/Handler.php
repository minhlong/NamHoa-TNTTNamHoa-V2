<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

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
     * @param  Throwable  $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException ||
            $exception instanceof MethodNotAllowedHttpException ||
            $exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Không tìm thấy.',
            ], 404);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'error' => $exception->errors(),
            ], 400);
        }

        if ($this->isHttpException($exception)) {
            switch ($exception->getStatusCode()) {
                case 403:
                    return response()->json([
                        'error' => 'Bạn chưa được phân quyền cho thao tác này!',
                    ], $exception->getStatusCode());
                    break;
                case 401:
                    return response()->json([
                        'error' => "Hết phiên đăng nhập, vui lòng đăng nhập lại.",
                    ], $exception->getStatusCode());
                    break;
                default:
                    break;
            }
        }

        return parent::render($request, $exception);
    }
}
