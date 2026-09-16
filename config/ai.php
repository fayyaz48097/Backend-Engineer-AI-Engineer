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

];