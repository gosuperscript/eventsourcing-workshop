<?php

use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalFailed;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

return [
    'class_map' => [
        TokensDeposited::class => 'tokens_deposited',
        TokensWithdrawn::class => 'tokens_withdrawn',
        WithdrawalFailed::class => 'withdrawal_failed',
        WalletId::class => 'wallet_id',
        Wallet::class => 'wallet',
    ]
];
