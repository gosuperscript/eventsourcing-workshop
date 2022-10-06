<?php

namespace Workshop\Domains\FraudDetection\Commands;

class ApproveTransaction
{
    public function __construct(
        public string $transactionId,
    )
    {
    }
}
