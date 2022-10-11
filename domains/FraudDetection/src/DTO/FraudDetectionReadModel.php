<?php

namespace Workshop\Domains\FraudDetection\DTO;

class FraudDetectionReadModel
{
    public function __construct(
        public readonly string $transactionId,
        public readonly string $tokens,
    )
    {

    }
}
