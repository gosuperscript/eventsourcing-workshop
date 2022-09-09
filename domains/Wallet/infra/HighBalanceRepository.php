<?php

namespace Workshop\Domains\Wallet\Infra;

use Workshop\Domains\Wallet\WalletId;

interface HighBalanceRepository
{
    public function getBalance(WalletId $walletId): int;

    public function setBalance(WalletId $walletId, int $balance): void;
}
