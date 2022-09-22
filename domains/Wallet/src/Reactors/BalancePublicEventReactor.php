<?php

namespace Workshop\Domains\Wallet\Reactors;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;
use EventSauce\EventSourcing\EventConsumption\InflectHandlerMethodsFromType;
use EventSauce\EventSourcing\Message;
use Robertbaelde\PersistingMessageBus\MessageDispatcher;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\PublicEvents\Balance\BalanceUpdated;

class BalancePublicEventReactor extends EventConsumer
{
    public function __construct(
        private readonly MessageDispatcher $messageDispatcher,
        private readonly WalletBalanceRepository $walletBalanceRepository,
    )
    {
    }

    public function handleTransaction(TokensDeposited|TokensWithdrawn $event, Message $message): void
    {
        $balance = $this->walletBalanceRepository->getWalletTokens($message->aggregateRootId());

        $this->messageDispatcher->dispatch(new BalanceUpdated(
            balance: $balance,
        ));
    }

    protected function handleMethodInflector(): HandleMethodInflector
    {
        return new InflectHandlerMethodsFromType();
    }
}
