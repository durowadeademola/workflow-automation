<?php

namespace App\Workflow;

use App\Workflow\Contracts\StepHandler;
use RuntimeException;

class StepRegistry
{
    public function resolve(string $type): StepHandler
    {
        $class = config("workflow.steps.{$type}");

        if (! $class) {
            throw new RuntimeException("No step handler registered for workflow step type [{$type}]. Register it in config/workflow.php.");
        }

        return app($class);
    }
}
