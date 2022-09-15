<?php

namespace Workshop\Domains\Wallet\ReadModels;

use Carbon\Carbon;

interface TransactionsReadModelRepository
{
    public function addTransaction(
        string $eventId,
        string $walletId,
        int $amount,
        Carbon $transactedAt
    ): void;
}
