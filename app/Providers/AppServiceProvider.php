<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Contracts\AIPromptBuilder;
use App\Services\AI\Contracts\AIResponseParser;
// use App\Services\AI\Gemini\GeminiClient;
use App\Services\AI\Factories\AIClientFactory;
use App\Services\AI\Gemini\GeminiResponseParser;
use App\Services\AI\Prompts\TicketPromptBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AIClient::class,
            function ($app) {
                return $app
                    ->make(AIClientFactory::class)
                    ->make();
            }
        );
        $this->app->bind(AIResponseParser::class, GeminiResponseParser::class);
        $this->app->bind(AIPromptBuilder::class, TicketPromptBuilder::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}