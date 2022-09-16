<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\Infra\HighBalanceRepository;
use Workshop\Domains\Wallet\WalletId;

class InMemoryHighBalanceRepository implements HighBalanceRepository
{

    private array $wallets = [];

    public function getBalance(WalletId $walletId): int
    {
        if(array_key_exists($walletId->toString(), $this->wallets)){
            return $this->wallets[$walletId->toString()];
        }
        return 0;
    }

    public function setBalance(WalletId $walletId, int $balance): void
    {
        $this->wallets[$walletId->toString()] = $balance;
    }
}
