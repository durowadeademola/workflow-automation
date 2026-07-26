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
        'rag_search' => \App\Workflow\Steps\RagSearchStep::class,
        'chat_prompt_builder' => \App\Workflow\Steps\ChatPromptBuilderStep::class,
        'llm_call' => \App\Workflow\Steps\LlmCallStep::class,
        'extract_markers' => \App\Workflow\Steps\ExtractMarkersStep::class,
        'dispatch_handoff' => \App\Workflow\Steps\DispatchHandoffStep::class,
        'dispatch_appointment' => \App\Workflow\Steps\DispatchAppointmentStep::class,
        'dispatch_lead' => \App\Workflow\Steps\DispatchLeadStep::class,
        'dispatch_registration' => \App\Workflow\Steps\DispatchRegistrationStep::class,
        'chat_response_builder' => \App\Workflow\Steps\ChatResponseBuilderStep::class,
    ],

];
