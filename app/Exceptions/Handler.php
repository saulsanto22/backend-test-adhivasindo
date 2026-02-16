<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        ExternalDataException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (ExternalDataException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        });

        $this->renderable(function (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        });

        $this->renderable(function (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        });

        $this->renderable(function (QueryException $e) {
            $message = 'Database connection error.';
            $code = 503;

            if (str_contains($e->getMessage(), 'connection') || str_contains($e->getMessage(), 'SQLSTATE[08006]') || str_contains($e->getMessage(), 'SQLSTATE[HY000]')) {
                $message = 'Tidak dapat terhubung ke database. Pastikan database sudah berjalan dan konfigurasi sudah benar.';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
            ], $code);
        });
    }
}
