<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\WalletId;

class InMemoryWalletBalanceRepository implements WalletBalanceRepository
{

    private array $wallets = [];

    public function getWallets(): array
    {
        return $this->wallets;
    }

    public function getWalletTokens(WalletId $walletId): int
    {
        if(array_key_exists($walletId->toString(), $this->wallets)){
            return $this->wallets[$walletId->toString()];
        }
        return 0;
    }

    public function updateWalletTokens(WalletId $walletId, int $tokens): void
    {
        $this->wallets[$walletId->toString()] = $tokens;
    }
}
