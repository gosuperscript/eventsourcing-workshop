<?php

namespace Workshop\Domains\FraudDetection\Commands;

class RejectTransaction
{
    public function __construct(
        public string $transactionId,
    )
    {
    }
}
