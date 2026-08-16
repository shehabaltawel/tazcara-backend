<?php

use App\Http\Middleware\Authenticate;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckAbility;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => Authenticate::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $exception, Request $request): mixed {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            [$status, $message, $errors] = match (true) {
                $exception instanceof InvalidArgumentException => [Response::HTTP_BAD_REQUEST, $exception->getMessage(), null],
                $exception instanceof ModelNotFoundException => [Response::HTTP_NOT_FOUND, 'Resource not found', null],
                $exception instanceof AuthenticationException => [Response::HTTP_UNAUTHORIZED, $exception->getMessage(), null],
                $exception instanceof ValidationException => [
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $exception->getMessage(),
                    $exception->errors(),
                ],
                $exception instanceof HttpException => [
                    $exception->getStatusCode(),
                    $exception->getMessage() !== '' ? $exception->getMessage() : Response::$statusTexts[$exception->getStatusCode()] ?? 'Error',
                    null,
                ],
                default => [Response::HTTP_INTERNAL_SERVER_ERROR, 'Something went wrong', null],
            };

            $response = [
                'error' => true,
                'message' => $message,
                'data' => [],
            ];

            if ($errors !== null) {
                $response['errors'] = $errors;
            }

            if (! app()->environment('production')) {
                $response['debug'] = $exception::class.': '.$exception->getMessage();
            }

            return response()->json($response, $status);
        });
    })->create();
