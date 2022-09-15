<?php

namespace Workshop\Domains\Wallet\Infra;

use Carbon\Carbon;
use Workshop\Domains\Wallet\ReadModels\Transaction;

class EloquentTransactionsRepository implements TransactionsReadModelRepository
{
    public function addTransaction(string $eventId, string $walletId, int $amount, Carbon $transactedAt): void
    {
        (new Transaction([
            'event_id' => $eventId,
            'wallet_id' => $walletId,
            'amount' => $amount,
            'transacted_at' => $transactedAt
        ]))->save();
    }
}