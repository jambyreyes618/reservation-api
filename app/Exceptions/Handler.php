<?php

namespace App\Exceptions;

use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponses;

    /**
     * Map exception classes to handler methods that produce standard error items.
     *
     * @var array<class-string<Throwable>, string>
     */
    protected array $handlers = [
        ValidationException::class => 'handleValidation',
        ModelNotFoundException::class => 'handleModelNotFound',
        NotFoundHttpException::class => 'handleNotFound',
        AuthenticationException::class => 'handleUnauthenticated',
        AuthorizationException::class => 'handleAuthorization',
    ];

    /**
     * Convert validation errors (422) into the standard error list.
     *
     * @return array<int, array{status: int, message: string, source: string}>
     */
    public function handleValidation(ValidationException $exception, Request $request): array
    {
        $errors = [];

        foreach ($exception->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'status' => 422,
                    'message' => $message,
                    'source' => $field,
                ];
            }
        }

        return $errors;
    }

    /**
     * Convert missing model errors (404) into the standard error list.
     *
     * @return array<int, array{status: int, message: string, source: string}>
     */
    public function handleModelNotFound(ModelNotFoundException $exception, Request $request): array
    {
        $model = class_basename($exception->getModel() ?? 'Resource');

        return [
            [
                'status' => 404,
                'message' => "The {$model} could not be found.",
                'source' => $model,
            ],
        ];
    }

    /**
     * Convert missing route / HTTP 404 errors into the standard error list.
     *
     * @return array<int, array{status: int, message: string, source: string}>
     */
    public function handleNotFound(NotFoundHttpException $exception, Request $request): array
    {
        $previous = $exception->getPrevious();

        if ($previous instanceof ModelNotFoundException) {
            return $this->handleModelNotFound($previous, $request);
        }

        return [
            [
                'status' => 404,
                'message' => 'The requested resource was not found.',
                'source' => $request->path(),
            ],
        ];
    }

    /**
     * Convert unauthenticated errors (401) into the standard error list.
     *
     * @return array<int, array{status: int, message: string, source: string}>
     */
    public function handleUnauthenticated(AuthenticationException $exception, Request $request): array
    {
        return [
            [
                'status' => 401,
                'message' => 'Unauthenticated.',
                'source' => '',
            ],
        ];
    }

    /**
     * Convert authorization errors (403) into the standard error list.
     *
     * @return array<int, array{status: int, message: string, source: string}>
     */
    public function handleAuthorization(AuthorizationException $exception, Request $request): array
    {
        return [
            [
                'status' => 403,
                'message' => $exception->getMessage() !== '' ? $exception->getMessage() : 'You are not authorized to perform this action.',
                'source' => '',
            ],
        ];
    }

    /**
     * Render any exception as a consistent JSON error response.
     */
    public function render($request, Throwable $e): JsonResponse|Response
    {
        if ($e instanceof HttpException && $e->getStatusCode() === 403 && !array_key_exists($e::class, $this->handlers)) {
            return $this->notAuthorized(
                $e->getMessage() !== '' ? $e->getMessage() : 'You are not authorized to perform this action.'
            );
        }

        foreach ($this->handlers as $exceptionClass => $method) {
            if ($e instanceof $exceptionClass) {
                return $this->error($this->{$method}($e, $request));
            }
        }

        $status = $e instanceof HttpException ? $e->getStatusCode() : 500;
        $message = config('app.debug')
            ? $e->getMessage()
            : ($status === 500 ? 'Server error.' : ($e->getMessage() !== '' ? $e->getMessage() : 'Request failed.'));
        $source = config('app.debug')
            ? $e->getFile() . ':' . $e->getLine()
            : '';

        if ($status >= 500) {
            Log::error($e->getMessage(), ['exception' => $e]);
        }

        return $this->error([
            [
                'status' => $status,
                'message' => $message,
                'source' => $source,
            ],
        ], $status);
    }
}