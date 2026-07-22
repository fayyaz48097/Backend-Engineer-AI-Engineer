<?php

namespace App\Services\AI\Validators;

use Illuminate\Support\Facades\Validator;

class TicketAnalysisResponseValidator
{
    public function validate(array $data): array
    {
        return Validator::make($data, [
            'category' => [
                'required',
                'string',
                'in:Technical,Billing,General,Sales',
            ],
            'priority' => [
                'required',
                'string',
                'in:Low,Medium,High',
            ],
            'summary' => [
                'required',
                'string',
            ],
            'suggested_reply' => [
                'required',
                'string',
            ],
        ])->validate();
    }
}