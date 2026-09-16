<?php

namespace App\Services\AI\Contracts;

use App\Data\AI\TicketAnalysisData;

interface AIResponseParser
{
    /**
     * Parse the raw AI response into a validated, typed object.
     */
    public function parse(array $rawResponse): TicketAnalysisData;
}
