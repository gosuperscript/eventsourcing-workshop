<?php

namespace Workshop\Domains\Wallet\Projectors;

use Carbon\Carbon;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\ReplayingMessages\TriggerBeforeReplay;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;

final class TransactionsProjector extends EventConsumer implements TriggerBeforeReplay
{

    public function __construct(private TransactionsReadModelRepository $transactionsReadModelRepository)
    {
    }

    public function beforeReplay(): void
    {
        $this->transactionsReadModelRepository->truncate();
    }

    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        $this->transactionsReadModelRepository->addTransaction(
            $message->headers()[Header::EVENT_ID],
            $message->aggregateRootId()->toString(),
            $event->tokens,
            Carbon::createFromImmutable($message->timeOfRecording()),
        );
    }

    public function handleTokensWithdrawn(TokensWithdrawn $event, Message $message): void
    {
        $this->transactionsReadModelRepository->addTransaction(
            $message->headers()[Header::EVENT_ID],
            $message->aggregateRootId()->toString(),
            -$event->tokens,
            Carbon::createFromImmutable($message->timeOfRecording())
        );
    }


}
