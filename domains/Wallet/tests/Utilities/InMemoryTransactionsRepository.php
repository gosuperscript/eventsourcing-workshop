<?php

namespace Workshop\Domains\Wallet\Tests\Utilities;

use Carbon\Carbon;
use Workshop\Domains\Wallet\ReadModels\TransactionsReadModelRepository;

class InMemoryTransactionsRepository implements TransactionsReadModelRepository
{

    private array $transactions = [];

    public function addTransaction(string $eventId, string $walletId, int $amount, Carbon $transactedAt): void
    {
        $this->transactions[] = [
            'eventId' => $eventId,
            'walletId' => $walletId,
            'amount' => $amount,
            'transactedAt' => $transactedAt,
        ];
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }
}
