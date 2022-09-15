<?php

namespace Workshop\Domains\Wallet\Infra;

use Workshop\Domains\Wallet\WalletId;

interface WalletBalanceRepository
{
    public function getWallets(): array;

    public function getWalletTokens(WalletId $walletId): int;

    public function updateWalletTokens(WalletId $walletId, int $tokens): void;
}
