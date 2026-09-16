<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | Must be a key registered in App\Services\AI\Factories\AIClientFactory.
    | Use "fake" to run the whole pipeline with no network call.
    |
    */

    'provider' => env('AI_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | Timeouts (seconds)
    |--------------------------------------------------------------------------
    */

    'timeout' => env('AI_TIMEOUT', 30),

    'connect_timeout' => env('AI_CONNECT_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Structured Output
    |--------------------------------------------------------------------------
    |
    | Ask the provider to constrain its answer to the prompt builder's schema
    | instead of merely requesting JSON in the prompt. Not every model
    | supports it, so this is a kill switch: set AI_STRUCTURED_OUTPUT=false to
    | fall back to prompt-only JSON without shipping code.
    |
    */

    'structured_output' => env('AI_STRUCTURED_OUTPUT', true),

    /*
    |--------------------------------------------------------------------------
    | Retry
    |--------------------------------------------------------------------------
    |
    | Applied at the HTTP level for connection failures and retryable status
    | codes. Delay is the base for exponential backoff: 2s, 4s, 8s...
    |
    */

    'retry' => [

        'times' => env('AI_RETRY_TIMES', 3),

        'delay' => env('AI_RETRY_DELAY', 1000),

    ],

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'gemini' => [
            'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        ],

        'openai' => [
            'model' => env('OPENAI_MODEL', 'gpt-5'),
        ],

        'fake' => [
            'enabled' => env('FAKE_AI', false),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Email Automation
    |--------------------------------------------------------------------------
    |
    | Three independent switches, all OFF by default, so deploying this code
    | changes nothing until you deliberately turn a stage on:
    |
    |   enabled          generate and store drafts (writes to the DB only)
    |   auto_approve     skip the human approval step
    |   sending_enabled  actually hand the mail to the mailer
    |
    | Roll them on in that order and you can never surprise a real customer.
    |
    */

    'email' => [

        'enabled' => env('AI_EMAIL_ENABLED', false),

        'auto_approve' => env('AI_EMAIL_AUTO_APPROVE', false),

        'sending_enabled' => env('AI_EMAIL_SENDING_ENABLED', false),

        'max_body_length' => env('AI_EMAIL_MAX_BODY_LENGTH', 5000),

    ],

];
