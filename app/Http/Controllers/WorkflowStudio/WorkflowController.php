<?php

namespace App\Http\Controllers\WorkflowStudio;

use App\Http\Controllers\Controller;
use App\Models\AutomationWorkflow;
use App\Workflow\WorkflowExecutor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    public function index()
    {
        return AutomationWorkflow::withCount('runs')
            ->orderByDesc('id')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:automation_workflows,slug'],
            'trigger_type' => ['required', Rule::in(['manual', 'webhook', 'scheduled', 'model_event'])],
            'trigger_config' => ['nullable', 'array'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $workflow = AutomationWorkflow::create($validated);

        return response()->json($workflow, 201);
    }

    public function show(AutomationWorkflow $workflow)
    {
        return $workflow->load('steps');
    }

    public function update(Request $request, AutomationWorkflow $workflow)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'alpha_dash', Rule::unique('automation_workflows', 'slug')->ignore($workflow->id)],
            'trigger_type' => ['sometimes', Rule::in(['manual', 'webhook', 'scheduled', 'model_event'])],
            'trigger_config' => ['sometimes', 'nullable', 'array'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $workflow->update($validated);

        return $workflow->fresh();
    }

    public function destroy(AutomationWorkflow $workflow)
    {
        $workflow->delete();

        return response()->json(['status' => 'deleted']);
    }

    /**
     * Populates the "add step" dropdown from the same registry the engine
     * itself reads — a step type is available in the builder the moment
     * it's registered in config('workflow.steps'), nothing else to wire up.
     */
    public function stepTypes()
    {
        return collect(config('workflow.steps', []))
            ->map(fn ($class, $type) => [
                'type' => $type,
                'label' => Str::of(class_basename($class))
                    ->replaceMatches('/(?<!^)([A-Z])/', ' $1')
                    ->__toString(),
                'class' => $class,
            ])
            ->values();
    }

    /**
     * Runs the workflow right now with an ad-hoc trigger payload, so you can
     * see it actually work while building it — same WorkflowExecutor real
     * traffic uses, so this is a genuine run, not a simulation (any dispatch
     * step really will call its controller).
     */
    public function testRun(Request $request, AutomationWorkflow $workflow, WorkflowExecutor $executor)
    {
        $validated = $request->validate([
            'trigger_payload' => ['nullable', 'array'],
        ]);

        $run = $executor->run($workflow, $validated['trigger_payload'] ?? []);

        return $run->load('runSteps');
    }
}
