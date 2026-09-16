<?php

namespace App\Services\AI\Email;

use App\Data\AI\GeneratedEmailData;
use App\Services\AI\Contracts\AIJsonExtractor;
use App\Services\AI\Validators\GeneratedEmailValidator;

/**
 * Provider-agnostic: the only provider-specific step (unwrapping the
 * envelope) is delegated to the injected extractor.
 */
class EmailResponseParser
{
    public function __construct(
        protected AIJsonExtractor $extractor,
        protected GeneratedEmailValidator $validator,
    ) {}

    public function parse(array $rawResponse): GeneratedEmailData
    {
        $result = $this->validator->validate(
            $this->extractor->extract($rawResponse)
        );

        return new GeneratedEmailData(
            subject: $result['subject'],
            body: $result['body'],
        );
    }
}
