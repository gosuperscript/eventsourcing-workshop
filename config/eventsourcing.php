<?php

use Workshop\Domains\Wallet\TokensDeposited;
use Workshop\Domains\Wallet\TokensWithdrawn;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;
use Workshop\Domains\Wallet\WithdrawalRefused;

return [
    'class_map' => [
        Wallet::class => 'wallet.wallet',
        WalletId::class => 'wallet.wallet_id',
        TokensDeposited::class => 'wallet.tokens_deposited',
        TokensWithdrawn::class => 'wallet.tokens_withdrawn',
        WithdrawalRefused::class => 'wallet.withdrawal_refused',
    ]
];