<?php

namespace App\Http\Controllers\WorkflowStudio;

use App\Http\Controllers\Controller;
use App\Models\AutomationWorkflow;
use App\Models\AutomationWorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkflowStepController extends Controller
{
    public function store(Request $request, AutomationWorkflow $workflow)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('automation_workflow_steps', 'key')->where('automation_workflow_id', $workflow->id)],
            'type' => ['required', 'string', Rule::in(array_keys(config('workflow.steps', [])))],
            'config' => ['nullable', 'array'],
            'run_if' => ['nullable', 'array'],
            'canvas_position' => ['nullable', 'array'],
        ]);

        $validated['order'] = ($workflow->steps()->max('order') ?? 0) + 1;

        $step = $workflow->steps()->create($validated);

        return response()->json($step, 201);
    }

    public function update(Request $request, AutomationWorkflowStep $step)
    {
        $validated = $request->validate([
            'key' => ['sometimes', 'required', 'string', 'max:255', 'alpha_dash', Rule::unique('automation_workflow_steps', 'key')->where('automation_workflow_id', $step->automation_workflow_id)->ignore($step->id)],
            'type' => ['sometimes', 'required', 'string', Rule::in(array_keys(config('workflow.steps', [])))],
            'config' => ['sometimes', 'nullable', 'array'],
            'run_if' => ['sometimes', 'nullable', 'array'],
            'canvas_position' => ['sometimes', 'nullable', 'array'],
            'order' => ['sometimes', 'integer', 'min:1'],
        ]);

        $step->update($validated);

        return $step->fresh();
    }

    public function destroy(AutomationWorkflowStep $step)
    {
        $step->delete();

        return response()->json(['status' => 'deleted']);
    }

    /**
     * Bulk order update after a drag-to-reorder in the builder — cheaper
     * than one PUT per step, and keeps the list atomic (no half-applied
     * reorder if the request is cut off partway).
     */
    public function reorder(Request $request, AutomationWorkflow $workflow)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', Rule::exists('automation_workflow_steps', 'id')->where('automation_workflow_id', $workflow->id)],
        ]);

        foreach ($validated['order'] as $index => $stepId) {
            AutomationWorkflowStep::where('id', $stepId)->update(['order' => $index + 1]);
        }

        return $workflow->load('steps');
    }
}
