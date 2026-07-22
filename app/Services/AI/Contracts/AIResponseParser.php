<?php

namespace App\Services\AI\Contracts;

use App\Data\AI\TicketAnalysisData;

interface AIResponseParser
{
    /**
     * Parse the raw AI response into a clean associative array.
     */
    public function parse(array $response): TicketAnalysisData;
}