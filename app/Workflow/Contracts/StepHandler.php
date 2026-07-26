<?php

namespace App\Workflow\Contracts;

use App\Workflow\WorkflowContext;

/**
 * One handler class per step `type`, registered in config('workflow.steps').
 * This is the engine's only real extension point — a new workflow that needs
 * logic no existing step covers means writing one of these, not touching the
 * executor itself.
 */
interface StepHandler
{
    /**
     * @param array<string, mixed> $config The step's own config, already
     *   resolved (any "{{path}}" placeholders in it swapped for real values
     *   from $context).
     * @return array<string, mixed> Stored as this step's output, addressable
     *   by later steps as "steps.<key>.<field>".
     */
    public function execute(array $config, WorkflowContext $context): array;
}
