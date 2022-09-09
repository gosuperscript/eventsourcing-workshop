<?php

namespace Workshop\Domains\Wallet\Infra;

use Workshop\Domains\Wallet\WalletId;

interface NotificationService
{
    public function sendWalletHighBalanceNotification(WalletId $walletId): void;
}
