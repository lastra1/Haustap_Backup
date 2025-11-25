<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Temporarily disable Filament provider to isolate boot errors
    ->withProviders([
        \App\Providers\Filament\AdminPanelProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Use Laravel built-in CORS handler
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (!($request->expectsJson() || str_contains($request->header('Accept', ''), 'application/json'))) {
                return null;
            }

            $status = 500;
            $payload = [
                'success' => false,
                'message' => 'Server error',
            ];

            if ($e instanceof ValidationException) {
                $status = 422;
                $payload['message'] = 'Validation failed';
                $payload['errors'] = $e->errors();
            } elseif ($e instanceof AuthenticationException) {
                $status = 401;
                $payload['message'] = 'Unauthenticated';
            } elseif ($e instanceof AuthorizationException) {
                $status = 403;
                $payload['message'] = 'Forbidden';
            } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                $status = 404;
                $payload['message'] = 'Not Found';
            } elseif ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $payload['message'] = $e->getMessage() ?: 'HTTP error';
            } else {
                $payload['message'] = (bool) config('app.debug') ? ($e->getMessage() ?: 'Server error') : 'Server error';
            }

            $payload['path'] = $request->path();
            $payload['method'] = $request->method();

            Log::error('API exception', [
                'status' => $status,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'path' => $request->path(),
                'method' => $request->method(),
            ]);

            return response()->json($payload, $status);
        });
    })->create();