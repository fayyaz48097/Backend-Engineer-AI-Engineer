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

        logger()->info('AI execution started.', [
            'request_id' => $requestId,
            'provider' => config('ai.provider'),
            ...$context,
        ]);

        try {
            $result = $callback();

            $duration = (int) round(
                (microtime(true) - $start) * 1000
            );

            logger()->info('AI execution completed.', [
                'request_id' => $requestId,
                'provider' => config('ai.provider'),
                'model' => config('ai.providers.gemini.model'),
                'duration_ms' => $duration,
                ...$context,
            ]);

            return $result;
        } catch (Throwable $e) {
            $duration = (int) round(
                (microtime(true) - $start) * 1000
            );

            logger()->error('AI execution failed.', [
                'request_id' => $requestId,
                'provider' => config('ai.provider'),
                'duration_ms' => $duration,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                ...$context,
            ]);

            throw $e;
        }
    }
}