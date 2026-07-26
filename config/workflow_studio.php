<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Workflow Studio Credentials
    |--------------------------------------------------------------------------
    |
    | A single shared credential for the hidden workflow builder at
    | /workflow-studio — deliberately not tied to the `users` table or
    | Filament's auth at all, so it keeps working (or can be revoked)
    | completely independently of either.
    |
    */

    'username' => env('WORKFLOW_STUDIO_USERNAME'),
    'password_hash' => env('WORKFLOW_STUDIO_PASSWORD_HASH'),

];
