<?php

namespace Workshop\Domains\Wallet\Reactors;

use Carbon\Carbon;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\NotificationService;
use Illuminate\Support\Facades\Cache;

final class NotificationsReactor extends EventConsumer
{
    private array $balances;
    private array $notifications;

    public function __construct(private NotificationService $notificationService)
    {
        $this->balances = Cache::get('balances', []);
        $this->notifications = Cache::get('notifications', []);
    }

    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        $walletId = $message->aggregateRootId()->toString();

        if (!isset($this->balances[$walletId])) {
            $this->balances[$walletId] = [
                'tokens' => 0
            ];
        }

        if (!isset($this->notifications[$walletId])) {
            $this->notifications[$walletId] = [];
        }

        $this->balances[$walletId]['tokens'] += $event->tokens();
        $this->balances[$walletId]['transacted_at'] = Carbon::createFromImmutable($message->timeOfRecording());

        if ($this->balances[$walletId]['tokens'] > 100 && empty($this->notifications[$walletId])) {
            $this->notificationService->sendWalletHighBalanceNotification($message->aggregateRootId());

            $this->notifications[$walletId]['sent_at'] = Carbon::createFromImmutable($message->timeOfRecording());
        }
    }


    public function handleTokensWithdrawn(TokensWithdrawn $event, Message $message): void
    {
        $walletId = $message->aggregateRootId()->toString();

        $this->balances[$walletId]['tokens'] -= $event->tokens();

        if ($this->balances[$walletId]['tokens'] <= 100) {
            unset($this->notifications[$walletId]);
        }
    }

    public function __destruct()
    {
        Cache::put('balances', $this->balances);
        Cache::put('notifications', $this->notifications);
    }
}