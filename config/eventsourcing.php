<?php

use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalRefused;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

return [
    'class_map' => [
        Wallet::class => 'wallet.wallet',
        WalletId::class => 'wallet.wallet_id',
        TokensDeposited::class => 'wallet.tokens_deposited',
        TokensWithdrawn::class => 'wallet.tokens_withdrawn',
        WithdrawalRefused::class => 'wallet.withdrawal_refused',
    ]
];