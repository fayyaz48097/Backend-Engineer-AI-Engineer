<?php

namespace App\Services\AI\Prompts;

use App\Services\AI\Contracts\AIPromptBuilder;

class TicketPromptBuilder implements AIPromptBuilder
{
    public function build(string $subject, string $message): string
    {
        return <<<PROMPT
You are an AI customer support assistant.

Analyze the following support ticket.

Return ONLY valid JSON.

The JSON must contain exactly these keys:

{
    "category":"",
    "priority":"",
    "summary":"",
    "suggested_reply":""
}

Category must be one of:

- Technical
- Billing
- General
- Sales

Priority must be one of:

- Low
- Medium
- High

Subject:
{$subject}

Message:
{$message}
PROMPT;
    }
}