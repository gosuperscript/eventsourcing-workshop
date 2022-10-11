<?php

namespace Workshop\Domains\Wallet\Commands;

use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\WalletId;

class TransferTokens
{
    public function __construct(
        public readonly TransactionId $transactionId,
        public readonly WalletId $sendingWalletId,
        public readonly WalletId $receivingWalletId,
        public readonly int $tokens,
        public readonly string $description,
    )
    {
    }
}
