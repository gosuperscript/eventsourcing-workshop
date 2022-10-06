<?php

namespace Workshop\Domains\Wallet\Transactions;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;
use EventSauce\EventSourcing\EventConsumption\InflectHandlerMethodsFromType;
use EventSauce\EventSourcing\Message;
use League\Tactician\CommandBus;
use Workshop\Domains\FraudDetection\Commands\RequestFraudDetection;
use Workshop\Domains\FraudDetection\Events\TransactionApproved;
use Workshop\Domains\ProcessManager\ProcessManager;
use Workshop\Domains\Wallet\Commands\DepositTokens;
use Workshop\Domains\Wallet\Commands\WithdrawTokens;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\TransferInitiated;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

class HighValueTransactionProcessManager extends EventConsumer implements ProcessManager
{
    private WalletId $sendingWalletId;
    private WalletId $receivingWalletId;
    private int $tokens;
    private string $description;
    private TransactionId $transactionId;

    public function __construct(
        private readonly CommandBus $commandBus,
    ) {
    }

    public function startsOn(Message $message): bool
    {
        $event = $message->payload();

        return $event instanceof TransferInitiated && $event->tokens >= 100;
    }

    public function transactionStarts(TransferInitiated $event, Message $message): void
    {
        $this->sendingWalletId = WalletId::fromString($message->aggregateRootId()->toString());
        $this->receivingWalletId = $event->receivingWalletId;
        $this->tokens = $event->tokens;
        $this->description = $event->description;
        $this->transactionId = $event->transactionId;

        $this->commandBus->handle(new RequestFraudDetection(
            transactionId: $event->transactionId->toString(),
            tokens: $event->tokens,
        ));
    }

    public function transactionApproved(TransactionApproved $event, Message $message): void
    {
        $this->commandBus->handle(new WithdrawTokens(
            walletId: $this->sendingWalletId,
            tokens: $this->tokens,
            description: $this->description,
            transactionId: $this->transactionId,
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
            'sending_wallet_id' => $this->sendingWalletId->toString(),
            'receiving_wallet_id' => $this->receivingWalletId->toString(),
            'tokens' => $this->tokens,
            'description' => $this->description,
            'transaction_id' => $this->transactionId->toString(),
        ];
    }

    public function fromPayload(array $payload): void
    {
        $this->sendingWalletId = WalletId::fromString($payload['sending_wallet_id']);
        $this->receivingWalletId = WalletId::fromString($payload['receiving_wallet_id']);
        $this->tokens = $payload['tokens'];
        $this->description = $payload['description'];
        $this->transactionId = TransactionId::fromString($payload['transaction_id']);
    }

    protected function handleMethodInflector(): HandleMethodInflector
    {
        return new InflectHandlerMethodsFromType();
    }
}
