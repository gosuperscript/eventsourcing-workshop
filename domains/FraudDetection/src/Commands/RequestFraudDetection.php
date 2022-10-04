<?php

namespace Workshop\Domains\FraudDetection\Commands;

class RequestFraudDetection
{
    public function __construct(
        public readonly string $transactionId,
        public readonly int $tokens,
    )
    {

    }
}
