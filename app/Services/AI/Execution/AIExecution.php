<?php

namespace App\Services\AI\Execution;

use Illuminate\Support\Str;
use Throwable;

class AIExecution
{
    /**
     * Execute an AI operation with timing, request ID, and logging.
     */
    public function run(callable $callback, array $context = [])
    {
        $requestId = Str::uuid()->toString();

        $start = microtime(true);

        $base = [
            'request_id' => $requestId,
            'provider' => config('ai.provider'),
            // Read the model of whichever provider is active, not a hardcoded one.
            'model' => config('ai.providers.'.config('ai.provider').'.model'),
            ...$context,
        ];

        logger()->info('AI execution started.', $base);

        try {
            $result = $callback();

            logger()->info('AI execution completed.', [
                ...$base,
                'duration_ms' => $this->elapsed($start),
            ]);

            return $result;
        } catch (Throwable $e) {
            logger()->error('AI execution failed.', [
                ...$base,
                'duration_ms' => $this->elapsed($start),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function elapsed(float $start): int
    {
        return (int) round((microtime(true) - $start) * 1000);
    }
}
