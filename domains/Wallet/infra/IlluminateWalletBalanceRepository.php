<?php

namespace Workshop\Domains\Wallet\Infra;

use Workshop\Domains\Wallet\WalletId;

class IlluminateWalletBalanceRepository implements WalletBalanceRepository
{
    const TABLE = 'wallet_balance';

    public function getWallets(): array
    {
        return \DB::table(self::TABLE)->get()->mapWithKeys(function ($row) {
            return [$row->wallet_id => $row->tokens];
        })->toArray();
    }

    public function getWalletTokens(WalletId $walletId): int
    {
        return \DB::table(self::TABLE)
            ->where('wallet_id', $walletId->toString())
            ->first()->tokens ?? 0;
    }

    public function updateWalletTokens(WalletId $walletId, int $tokens): void
    {
        \DB::table(self::TABLE)->updateOrInsert(
            ['wallet_id' => $walletId->toString()],
            ['tokens' => $tokens]
        );
    }
}
