<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AutomationWorkflow;
use App\Workflow\WorkflowExecutor;
use Illuminate\Http\Request;

/**
 * Generic entry point for any workflow whose trigger_type is "webhook" — a
 * new workflow only needs steps + a trigger_type of "webhook" to be callable
 * here, no new controller/route required. Protected by the same
 * webhook.secret middleware every other server-to-server endpoint uses.
 */
class WorkflowTriggerController extends Controller
{
    public function trigger(Request $request, string $slug, WorkflowExecutor $executor)
    {
        $workflow = AutomationWorkflow::where('slug', $slug)->where('is_active', true)->first();

        if (! $workflow) {
            abort(404, "No active workflow found for slug [{$slug}].");
        }

        if ($workflow->trigger_type !== 'webhook') {
            abort(422, "Workflow [{$slug}] has trigger_type [{$workflow->trigger_type}], not \"webhook\" — it can't be triggered this way.");
        }

        $run = $executor->run($workflow, $request->all());

        return response()->json([
            'run_id' => $run->id,
            'status' => $run->status,
            'result' => $run->context['steps'] ?? [],
            'error' => $run->error,
        ], $run->status === 'completed' ? 200 : 500);
    }
}
