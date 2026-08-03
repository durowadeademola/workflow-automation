<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Native Workflow Engine
    |--------------------------------------------------------------------------
    |
    | Which engine handles a given workflow's traffic. Chat widget messages
    | default to "n8n" (the live, current behaviour) — set
    | WIDGET_CHAT_ENGINE=native in .env to route them through the native
    | AutomationWorkflow engine instead, once it's been tested.
    |
    */

    'widget_chat_engine' => env('WIDGET_CHAT_ENGINE', 'n8n'),

    /*
    |--------------------------------------------------------------------------
    | Step Type Registry
    |--------------------------------------------------------------------------
    |
    | Maps an AutomationWorkflowStep's `type` to the handler class that runs
    | it. Adding a new step type for a future workflow means writing a class
    | implementing App\Workflow\Contracts\StepHandler and registering it here
    | — nothing else in the engine needs to change.
    |
    */

    'steps' => [
        //Blueflow Chat Widget Steps
        'rag_search' => \App\Workflow\Steps\ChatWidget\RagSearchStep::class,
        'chat_prompt_builder' => \App\Workflow\Steps\ChatWidget\ChatPromptBuilderStep::class,
        'llm_call' => \App\Workflow\Steps\ChatWidget\LlmCallStep::class,
        'extract_markers' => \App\Workflow\Steps\ChatWidget\ExtractMarkersStep::class,
        'dispatch_handoff' => \App\Workflow\Steps\ChatWidget\DispatchHandoffStep::class,
        'dispatch_appointment' => \App\Workflow\Steps\ChatWidget\DispatchAppointmentStep::class,
        'dispatch_lead' => \App\Workflow\Steps\ChatWidget\DispatchLeadStep::class,
        'dispatch_registration' => \App\Workflow\Steps\ChatWidget\DispatchRegistrationStep::class,
        'chat_response_builder' => \App\Workflow\Steps\ChatWidget\ChatResponseBuilderStep::class,
        
        //Blueflow Crawler Steps
        'delete_old_chunks' => \App\Workflow\Steps\Crawler\DeleteOldChunksStep::class,
        'fetch_pages' => \App\Workflow\Steps\Crawler\FetchPagesStep::class,
        'extract_and_chunk' => \App\Workflow\Steps\Crawler\ExtractAndChunkStep::class,
        'generate_embeddings' => \App\Workflow\Steps\Crawler\GenerateEmbeddingsStep::class,
        'store_chunks' => \App\Workflow\Steps\Crawler\StoreChunksStep::class,
        'crawl_summary' => \App\Workflow\Steps\Crawler\CrawlSummaryStep::class,

        //Blueflow Marketing Journey Steps — advanced by AdvanceMarketingJourneys
        //(JourneyStepAdvancer), not by WorkflowExecutor. Only send_email is a
        //real send today; the others gracefully no-op until real provider
        //credentials exist (see config('services.whatsapp'|'sms'|'telegram')).
        'send_email' => \App\Workflow\Steps\Marketing\SendEmailStep::class,
        'send_whatsapp' => \App\Workflow\Steps\Marketing\SendWhatsAppStep::class,
        'send_sms' => \App\Workflow\Steps\Marketing\SendSmsStep::class,
        'send_telegram' => \App\Workflow\Steps\Marketing\SendTelegramStep::class,
    ],

];
