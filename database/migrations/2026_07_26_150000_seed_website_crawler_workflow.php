<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seeds the native engine's second workflow: a faithful port of
     * n8n/Blueflow Crawler.json, but parameterized by trigger payload
     * (clientId/websiteUrl/paths) instead of hardcoded per-client values —
     * one workflow definition crawls every client, unlike the n8n version
     * which needs its "Client Config" node hand-edited per client.
     *
     * trigger_type=webhook: trigger via
     *   POST /api/workflows/website-crawler/trigger
     *   { "clientId": "4", "websiteUrl": "http://blueflow.test", "paths": ["/api/widget-knowledge"] }
     * (same webhook.secret header every other server-to-server call here uses).
     */
    public function up(): void
    {
        $workflowId = DB::table('automation_workflows')->insertGetId([
            'name' => 'Website Crawler',
            'slug' => 'website-crawler',
            'trigger_type' => 'webhook',
            'trigger_config' => null,
            'description' => 'Fetch pages -> strip HTML -> chunk -> embed -> store in Supabase, for RAG search at chat time. Native port of n8n/Blueflow Crawler.json.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $now = now();

        $steps = [
            [
                'key' => 'delete_old',
                'type' => 'delete_old_chunks',
                'config' => ['client_id' => '{{trigger.clientId}}'],
                'run_if' => null,
                'order' => 1,
            ],
            [
                'key' => 'fetch',
                'type' => 'fetch_pages',
                'config' => [
                    'client_id' => '{{trigger.clientId}}',
                    'website_url' => '{{trigger.websiteUrl}}',
                    'paths' => '{{trigger.paths}}',
                ],
                'run_if' => null,
                'order' => 2,
            ],
            [
                'key' => 'chunk',
                'type' => 'extract_and_chunk',
                'config' => [],
                'run_if' => null,
                'order' => 3,
            ],
            [
                'key' => 'embed',
                'type' => 'generate_embeddings',
                'config' => ['client_id' => '{{trigger.clientId}}'],
                'run_if' => null,
                'order' => 4,
            ],
            [
                'key' => 'store',
                'type' => 'store_chunks',
                'config' => [],
                'run_if' => null,
                'order' => 5,
            ],
            [
                'key' => 'summary',
                'type' => 'crawl_summary',
                'config' => [],
                'run_if' => null,
                'order' => 6,
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
        $workflowId = DB::table('automation_workflows')->where('slug', 'website-crawler')->value('id');

        if ($workflowId) {
            DB::table('automation_workflow_steps')->where('automation_workflow_id', $workflowId)->delete();
            DB::table('automation_workflows')->where('id', $workflowId)->delete();
        }
    }
};
