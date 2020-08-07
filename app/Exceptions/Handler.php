<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

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
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert the given exception to an array.
     *
     * @param \Exception $e
     *
     * @return array
     */
    protected function convertExceptionToArray(Exception $e)
    {
        $statusCode = $this->isHttpException($e) ? $e->getStatusCode() : 500;

        $resp = [
            'code' => $statusCode,
            'data' => [],
        ];

        if (config('app.debug')) {
            $resp['message'] = $e->getMessage();
            $resp['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->map(
                    function ($trace) {
                        return Arr::except($trace, ['args']);
                    }
                )->all(),
            ];
        } else {
            $resp['message'] = $this->isHttpException($e) ? $e->getMessage() : 'Server Error';
        }

        return $resp;
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Validation\ValidationException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json(
            [
                'code' => $exception->status,
                'message' => $exception->getMessage(),
                'data' => $exception->errors(),
            ],
            $exception->status
        );
    }
}
