<?php

namespace Workshop\Domains\Wallet\Reactors;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;
use EventSauce\EventSourcing\EventConsumption\InflectHandlerMethodsFromType;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\HighBalanceRepository;
use Workshop\Domains\Wallet\Infra\NotificationService;

class HighBalanceReactor extends EventConsumer
{
    public function __construct(private NotificationService $notificationService, private HighBalanceRepository $highBalanceRepository)
    {
    }

    protected function handleMethodInflector(): HandleMethodInflector
    {
        return new InflectHandlerMethodsFromType();
    }

    public function onTokensDeposited(TokensDeposited $event, Message $message)
    {
        $balance = $this->highBalanceRepository->getBalance($message->aggregateRootId());
        if($balance < 100 && ($balance + $event->tokens) >= 100){
            $this->notificationService->sendWalletHighBalanceNotification($message->aggregateRootId());
        }
        $this->highBalanceRepository->setBalance($message->aggregateRootId(), $balance + $event->tokens);
    }

    public function onTokensWithdrawn(TokensWithdrawn $event, Message $message)
    {
        $balance = $this->highBalanceRepository->getBalance($message->aggregateRootId());
        $this->highBalanceRepository->setBalance($message->aggregateRootId(), $balance - $event->tokens);
    }



}
