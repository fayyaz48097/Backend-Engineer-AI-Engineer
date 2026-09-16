<?php

namespace App\Providers;

use App\Services\AI\Contracts\AIClient;
use App\Services\AI\Contracts\AIJsonExtractor;
use App\Services\AI\Contracts\AIPromptBuilder;
use App\Services\AI\Contracts\AIResponseParser;
use App\Services\AI\Factories\AIClientFactory;
use App\Services\AI\Prompts\TicketPromptBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Client, parser and extractor all come from the factory, so
        // AI_PROVIDER alone decides which provider the whole pipeline uses.
        $this->app->bind(
            AIClient::class,
            fn ($app) => $app->make(AIClientFactory::class)->client()
        );

        $this->app->bind(
            AIResponseParser::class,
            fn ($app) => $app->make(AIClientFactory::class)->parser()
        );

        $this->app->bind(
            AIJsonExtractor::class,
            fn ($app) => $app->make(AIClientFactory::class)->jsonExtractor()
        );

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
