<?php

namespace App\Workflow;

use Illuminate\Support\Arr;

/**
 * Everything a workflow run knows so far: the payload it was triggered with,
 * plus each completed step's output, keyed by step `key`. Steps read from
 * this (via dot-paths like "trigger.message" or "steps.search.results") and
 * write their own output back into it as they run.
 */
class WorkflowContext
{
    private array $data;

    public function __construct(array $triggerPayload)
    {
        $this->data = ['trigger' => $triggerPayload, 'steps' => []];
    }

    public function get(string $path, mixed $default = null): mixed
    {
        return Arr::get($this->data, $path, $default);
    }

    public function setStepOutput(string $key, array $output): void
    {
        $this->data['steps'][$key] = $output;
    }

    public function all(): array
    {
        return $this->data;
    }

    /**
     * Resolves placeholders against the current context:
     * - A value that is exactly "{{path}}" is replaced with the resolved
     *   value itself (any type — array, bool, null, ...).
     * - A string containing "{{path}}" elsewhere is string-interpolated.
     * - Arrays are resolved recursively. A "$spread" key merges the array at
     *   that path into the parent array, so a step can forward an entire
     *   nested object (e.g. LLM-extracted appointment details) into another
     *   step's payload without re-declaring every field by name.
     * - Anything else passes through unchanged.
     */
    public function resolve(mixed $value): mixed
    {
        if (is_string($value)) {
            return $this->resolveString($value);
        }

        if (is_array($value)) {
            return $this->resolveArray($value);
        }

        return $value;
    }

    private function resolveString(string $value): mixed
    {
        if (preg_match('/^\{\{\s*([\w.]+)\s*\}\}$/', $value, $m)) {
            return $this->get($m[1]);
        }

        return preg_replace_callback('/\{\{\s*([\w.]+)\s*\}\}/', function ($m) {
            $resolved = $this->get($m[1]);

            return is_scalar($resolved) ? (string) $resolved : json_encode($resolved);
        }, $value);
    }

    private function resolveArray(array $value): array
    {
        $result = [];

        foreach ($value as $key => $item) {
            if ($key === '$spread') {
                $spread = $this->resolve($item);
                if (is_array($spread)) {
                    $result = array_merge($result, $spread);
                }
                continue;
            }

            $result[$key] = $this->resolve($item);
        }

        return $result;
    }
}
