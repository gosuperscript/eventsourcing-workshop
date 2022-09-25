<?php

namespace Workshop\Domains\Wallet\Infra;

use Carbon\Carbon;
use Workshop\Domains\Wallet\ReadModels\Notification;
use Workshop\Domains\Wallet\WalletId;

class EloquentNotificationService implements NotificationService
{
    public function sendWalletHighBalanceNotification(WalletId $walletId): void
    {
        \Illuminate\Support\Facades\Log::info(sprintf("Notified %s", $walletId->toString()));
    }
}
