<?php

namespace Workshop\Domains\Wallet\Tests\Utilities;

use Workshop\Domains\Wallet\Infra\NotificationService;
use Workshop\Domains\Wallet\WalletId;

class InMemoryNotificationService implements NotificationService
{

    private array $notifications = [];

    public function sendWalletHighBalanceNotification(WalletId $walletId): void
    {
        $this->notifications[] = $walletId->toString();
    }

    public function notificationSendExactlyOnceForWallet(WalletId $walletId): bool
    {
        return count(array_keys($this->notifications, $walletId->toString())) === 1;
    }
}
