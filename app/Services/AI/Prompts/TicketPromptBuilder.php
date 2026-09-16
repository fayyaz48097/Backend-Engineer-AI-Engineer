<?php

namespace App\Services\AI\Prompts;

use App\Data\AI\TicketAnalysisData;
use App\Services\AI\Contracts\AIPromptBuilder;

class TicketPromptBuilder implements AIPromptBuilder
{
    public function build(string $subject, string $message): string
    {
        $categories = $this->asList(TicketAnalysisData::CATEGORIES);
        $priorities = $this->asList(TicketAnalysisData::PRIORITIES);

        return <<<PROMPT
You are an AI customer support assistant.

Analyze the following support ticket and return the analysis as JSON.

Category must be one of:
{$categories}

Priority must be one of:
{$priorities}

The summary must be one or two sentences. The suggested reply must be a
complete, polite response addressed to the customer.

Subject:
{$subject}

Message:
{$message}
PROMPT;
    }

    public function responseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'category' => [
                    'type' => 'string',
                    'enum' => TicketAnalysisData::CATEGORIES,
                ],
                'priority' => [
                    'type' => 'string',
                    'enum' => TicketAnalysisData::PRIORITIES,
                ],
                'summary' => [
                    'type' => 'string',
                ],
                'suggested_reply' => [
                    'type' => 'string',
                ],
            ],
            'required' => [
                'category',
                'priority',
                'summary',
                'suggested_reply',
            ],
        ];
    }

    private function asList(array $values): string
    {
        return collect($values)
            ->map(fn (string $value) => "- {$value}")
            ->implode(PHP_EOL);
    }
}
