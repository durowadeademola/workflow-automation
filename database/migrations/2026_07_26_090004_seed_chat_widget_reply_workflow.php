<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seeds the native engine's first real workflow: a faithful port of
     * n8n/Blueflow Chat v2.json. Purely additive data — WIDGET_CHAT_ENGINE
     * still defaults to "n8n" in config/workflow.php, so this workflow
     * exists but handles zero live traffic until that's switched.
     */
    public function up(): void
    {
        $workflowId = DB::table('automation_workflows')->insertGetId([
            'name' => 'Chat Widget Reply',
            'slug' => 'chat-widget-reply',
            'trigger_type' => 'manual',
            'trigger_config' => null,
            'description' => 'RAG search -> prompt build -> LLM call -> marker extraction -> handoff/appointment/lead/registration dispatch. Native port of n8n/Blueflow Chat v2.json.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $now = now();

        $steps = [
            [
                'key' => 'search',
                'type' => 'rag_search',
                'config' => [
                    'text' => '{{trigger.message}}',
                    'client_id' => '{{trigger.clientId}}',
                ],
                'run_if' => null,
                'order' => 1,
            ],
            [
                'key' => 'prompt',
                'type' => 'chat_prompt_builder',
                'config' => [],
                'run_if' => null,
                'order' => 2,
            ],
            [
                'key' => 'llm',
                'type' => 'llm_call',
                'config' => [
                    // Matches n8n's disabled backup Groq node (n8n's active
                    // node is OpenRouter's free Llama 3.3 70B — switch
                    // provider to "openrouter" + set OPENROUTER_API_KEY in
                    // .env for exact parity once you have that key).
                    'provider' => 'groq',
                    'model' => 'llama-3.3-70b-versatile',
                    'temperature' => 0.3,
                    'max_tokens' => 512,
                    'system_prompt' => '{{steps.prompt.systemPrompt}}',
                    'user_message' => '{{steps.prompt.userMessage}}',
                ],
                'run_if' => null,
                'order' => 3,
            ],
            [
                'key' => 'extract',
                'type' => 'extract_markers',
                'config' => [
                    'text' => '{{steps.llm.text}}',
                ],
                'run_if' => null,
                'order' => 4,
            ],
            [
                'key' => 'dispatch_handoff',
                'type' => 'dispatch_handoff',
                'config' => [],
                'run_if' => ['field' => 'steps.extract.wantsHandoff', 'equals' => true],
                'order' => 5,
            ],
            [
                'key' => 'dispatch_appointment',
                'type' => 'dispatch_appointment',
                'config' => [],
                'run_if' => ['field' => 'steps.extract.wantsAppointment', 'equals' => true],
                'order' => 6,
            ],
            [
                'key' => 'dispatch_lead',
                'type' => 'dispatch_lead',
                'config' => [],
                'run_if' => ['field' => 'steps.extract.wantsLeadCapture', 'equals' => true],
                'order' => 7,
            ],
            [
                'key' => 'dispatch_registration',
                'type' => 'dispatch_registration',
                'config' => [],
                'run_if' => ['field' => 'steps.extract.wantsRegistration', 'equals' => true],
                'order' => 8,
            ],
            [
                'key' => 'respond',
                'type' => 'chat_response_builder',
                'config' => [],
                'run_if' => null,
                'order' => 9,
            ],
        ];

        foreach ($steps as $step) {
            DB::table('automation_workflow_steps')->insert([
                'automation_workflow_id' => $workflowId,
                'key' => $step['key'],
                'type' => $step['type'],
                'config' => json_encode($step['config']),
                'run_if' => $step['run_if'] ? json_encode($step['run_if']) : null,
                'order' => $step['order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $workflowId = DB::table('automation_workflows')->where('slug', 'chat-widget-reply')->value('id');

        if ($workflowId) {
            DB::table('automation_workflow_steps')->where('automation_workflow_id', $workflowId)->delete();
            DB::table('automation_workflows')->where('id', $workflowId)->delete();
        }
    }
};
