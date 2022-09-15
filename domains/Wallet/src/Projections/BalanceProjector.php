<?php

namespace Workshop\Domains\Wallet\Projections;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\ReadModels\BalanceReadModelRepository;

class BalanceProjector implements MessageConsumer
{
    private BalanceReadModelRepository $repository;

    public function __construct(BalanceReadModelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Message $message): void
    {
        $event = $message->payload();
        if ($event instanceof TokensDeposited) {
            $balance = $this->repository->getBalanceFor($message->aggregateRootId());
            $balance->balance += $event->amountOfTokens();
            $this->repository->save($balance);
        }
        if ($event instanceof TokensWithdrawn) {
            $balance = $this->repository->getBalanceFor($message->aggregateRootId());
            $balance->balance -= $event->amountOfTokens();
            $this->repository->save($balance);
        }
    }
}