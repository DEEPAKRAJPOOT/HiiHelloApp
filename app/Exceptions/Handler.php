<?php

namespace App\Exceptions;

use App\Models\ErrorLog as UserErrorLog;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        //you need to define all guard in  get_guard() method which in the helper
        $guard = Auth::user() ? get_guard() : "guest";

        UserErrorLog::updateOrCreate([
            'first_name' => Auth::user()->first_name ?? $guard,
            'email' => Auth::user()->email ?? null,
            'url' => $request->fullUrl(),
            'exception' => $exception->getMessage(),
            'ip_address' => $request->getClientIp(),
            'guard' => $guard
        ])->increment('count');

        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $current_gaurd = array_get($exception->guards(), 0);
        $route = route('login');

        if ($current_gaurd == 'admin') {
            $route = route('admin.login');
        }

        return $request->expectsJson()
        ? response()->json(['message' => $exception->getMessage()], 401)
        : redirect()->guest($route);
    }
}
