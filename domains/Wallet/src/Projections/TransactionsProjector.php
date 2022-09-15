<?php

namespace Workshop\Domains\Wallet\Projections;

use Carbon\Carbon;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\ReadModels\TransactionsReadModelRepository;

class TransactionsProjector implements MessageConsumer
{
    private TransactionsReadModelRepository $transactionsRepository;

    public function __construct(TransactionsReadModelRepository $transactionsRepository)
    {
        $this->transactionsRepository = $transactionsRepository;
    }

    public function handle(Message $message): void
    {
        $event = $message->payload();
        if ($event instanceof TokensDeposited) {
            $this->transactionsRepository->addTransaction(
                $message->header(Header::EVENT_ID),
                $message->aggregateRootId()->toString(),
                $event->amountOfTokens(),
                Carbon::parse($message->timeOfRecording())
            );
        }
        if ($event instanceof TokensWithdrawn) {
            $this->transactionsRepository->addTransaction(
                $message->header(Header::EVENT_ID),
                $message->aggregateRootId()->toString(),
                $event->amountOfTokens() * -1,
                Carbon::parse($message->timeOfRecording())
            );
        }
    }
}