<?php

namespace Workshop\Domains\Wallet\Commands;

use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\WalletId;

class WithdrawTokens
{
    public function __construct(
        public readonly WalletId $walletId,
        public readonly int $tokens,
        public readonly string $description,
        public readonly ?TransactionId $transactionId = null
    )
    {

    }
}
