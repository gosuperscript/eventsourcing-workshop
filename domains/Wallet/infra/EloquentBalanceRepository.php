<?php

namespace Workshop\Domains\Wallet\Infra;

use Workshop\Domains\Wallet\ReadModels\Balance;
use Workshop\Domains\Wallet\ReadModels\BalanceReadModelRepository;
use Workshop\Domains\Wallet\WalletId;

class EloquentBalanceRepository implements BalanceReadModelRepository
{
    public function getBalanceFor(WalletId $walletId): Balance
    {
        return Balance::find($walletId->toString()) ?: new Balance([
            'wallet_id' => $walletId->toString(),
            'balance' => 0,
        ]);
    }

    public function save(Balance $balance): void
    {
        $balance->save();
    }
}