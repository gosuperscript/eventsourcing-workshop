<?php
namespace Workshop\Domains\Wallet\Projectors;

use Illuminate\Support\Carbon;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;

class TransactionsProjector extends EventConsumer
{
    public function __construct(private TransactionsReadModelRepository $transactionsReadModelRepository)
    {
    }
 
    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        $this->transactionsReadModelRepository->addTransaction(
            eventId: $message->headers()[Header::EVENT_ID],
            walletId: $message->aggregateRootId()->toString(),
            amount: $event->tokens,
            transactedAt: Carbon::createFromImmutable($message->timeOfRecording())
        );
    }

    public function handleTokensWithdrawn(TokensWithdrawn $event, Message $message): void
    {
        $this->transactionsReadModelRepository->addTransaction(
            eventId: $message->headers()[Header::EVENT_ID],
            walletId: $message->aggregateRootId()->toString(),
            amount: $event->tokens * -1,
            transactedAt: Carbon::createFromImmutable($message->timeOfRecording())
        );
    }
}