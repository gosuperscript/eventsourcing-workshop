<?php

namespace Workshop\Domains\Wallet\ReadModels;

use Workshop\Domains\Wallet\WalletId;

interface BalanceReadModelRepository
{
    public function getBalanceFor(WalletId $walletId): Balance;

    public function save(Balance $balance): void;
}