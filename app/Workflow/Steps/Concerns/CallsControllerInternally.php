<?php

namespace App\Workflow\Steps\Concerns;

use Illuminate\Http\Request;

/**
 * Invokes an existing API controller's method in-process, the same way the
 * real /api/widget/... route would — reusing 100% of its validation and
 * business rules (subscription checks, limits, notifications) with no HTTP
 * round trip and no duplicated logic. The webhook-secret middleware that
 * protects those routes from outside callers is irrelevant here since we
 * never go through routing at all.
 */
trait CallsControllerInternally
{
    /**
     * @return array{status_code: int, body: array<string, mixed>}
     */
    protected function callController(string $controllerClass, string $method, array $payload): array
    {
        $request = Request::create('/', 'POST', $payload);
        $response = app($controllerClass)->{$method}($request);

        return [
            'status_code' => $response->getStatusCode(),
            'body' => json_decode($response->getContent(), true) ?? [],
        ];
    }
}
