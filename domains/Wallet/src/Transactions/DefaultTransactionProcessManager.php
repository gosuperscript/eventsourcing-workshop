<?php

namespace Workshop\Domains\Wallet\Transactions;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;
use EventSauce\EventSourcing\EventConsumption\InflectHandlerMethodsFromType;
use EventSauce\EventSourcing\Message;
use League\Tactician\CommandBus;
use Workshop\Domains\ProcessManager\ProcessManager;
use Workshop\Domains\Wallet\Commands\DepositTokens;
use Workshop\Domains\Wallet\Commands\WithdrawTokens;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\TransferInitiated;
use Workshop\Domains\Wallet\WalletId;

class DefaultTransactionProcessManager extends EventConsumer implements ProcessManager
{
    private WalletId $receivingWalletId;

    public function __construct(
        private readonly CommandBus $commandBus,
    )
    {
    }

    public function startsOn(Message $message): bool
    {
        $event = $message->payload();

        return $event instanceof TransferInitiated && $event->tokens < 100;
    }

    public function withdrawFromDebtor(TransferInitiated $event, Message $message): void
    {
        $this->receivingWalletId = $event->receivingWalletId;

        $this->commandBus->handle(new WithdrawTokens(
            walletId: WalletId::fromString($message->aggregateRootId()->toString()),
            tokens: $event->tokens,
            description: $event->description,
            transactionId: $event->transactionId,
        ));
    }

    public function depositToCreditor(TokensWithdrawn $event, Message $message): void
    {
        $this->commandBus->handle(new DepositTokens(
            walletId: $this->receivingWalletId,
            tokens: $event->tokens,
            description: $event->description,
            transactionId: $event->transactionId,
        ));
    }

    public function toPayload(): array
    {
        return [
            'receiving_wallet_id' => $this->receivingWalletId->toString(),
        ];
    }

    public function fromPayload(array $payload): void
    {
        $this->receivingWalletId = WalletId::fromString($payload['receiving_wallet_id']);
    }

    public function handleMethodInflector(): HandleMethodInflector
    {
        return new InflectHandlerMethodsFromType();
    }
}
