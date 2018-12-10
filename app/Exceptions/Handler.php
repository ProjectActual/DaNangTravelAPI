<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
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
    public function render($request, Exception $e)
    {
        if ($request->expectsJson()) {
            if($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return $this->errorsException('Error not found.', Response::HTTP_NOT_FOUND, $e->getMessage() ?: 'The current route does not exist.');
            }

            if($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->errorsException('Error not found.', Response::HTTP_NOT_FOUND, $e->getMessage() ?: 'Not found model.');
            }

            if($e instanceof \Illuminate\Auth\AuthenticationException) {
                return $this->errorsException('Unauthorization.', Response::HTTP_UNAUTHORIZED, $e->getMessage() ?: 'Unauthorization.');
            }

            if($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                return $this->errorsException('Method is not defined.', Response::HTTP_METHOD_NOT_ALLOWED, $e->getMessage() ?: 'Method is not defined.');
            }

            if($e instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
                return $this->errorsException('File size too large, can not upload file.', Response::HTTP_REQUEST_ENTITY_TOO_LARGE, $e->getMessage() ?: 'File size too large, can not upload file.');
            }

            if($e instanceof \Illuminate\Validation\ValidationException) {
                $errors = $e->validator->errors()->getMessages();
            }
            return $this->errorsException('Validation Error.', Response::HTTP_BAD_REQUEST, $errors);
        }
        return $this->errorsException("Internal Server Error", 500, $e->getMessage() ?? ('An exception of '.get_class_name($e)));
    }

    public function errorsException($message, $status, $errors)
    {
        return response()->json([
                'message'     => $message,
                'status'      => $status,
                'errors'      => $errors
            ], $status);
    }

    function get_class_name($object)
    {
        $classname = get_class($object);
        if ($pos = strrpos($classname, '\\')) {
            return substr($classname, $pos + 1);
        }

        return $classname;
    }
}
