<?php

namespace Workshop\Domains\Wallet\Transactions;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;
use EventSauce\EventSourcing\EventConsumption\InflectHandlerMethodsFromType;
use EventSauce\EventSourcing\Message;
use League\Tactician\CommandBus;
use Workshop\Domains\FraudDetection\Commands\ApproveTransaction;
use Workshop\Domains\FraudDetection\Commands\RequestFraudDetection;
use Workshop\Domains\FraudDetection\Events\TransactionApproved;
use Workshop\Domains\ProcessManager\ProcessManager;
use Workshop\Domains\Wallet\Commands\DepositTokens;
use Workshop\Domains\Wallet\Commands\WithdrawTokens;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Transactions\Events\TransferInitiated;
use Workshop\Domains\Wallet\WalletId;

class HighValueTransactionProcessManager extends EventConsumer implements ProcessManager
{
    private TransactionId $transactionId;
    private WalletId $debtorWalletId;
    private WalletId $receivingWalletId;
    private int $tokens;
    private string $description;
    private array $commands = [];

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function startsOn(Message $message): bool
    {
        $event = $message->payload();
        if(!$event instanceof TransferInitiated){
            return false;
        }
        if($event->tokens < 100){
            return false;
        }
        return true;
    }

    public function whenATransferStarts(TransferInitiated $transferInitiated, Message $message): void
    {
        $this->transactionId = $transferInitiated->transactionId;
        $this->debtorWalletId = $message->aggregateRootId();
        $this->receivingWalletId = $transferInitiated->receivingWalletId;
        $this->tokens = $transferInitiated->tokens;
        $this->description = $transferInitiated->description;

        $this->commands[] = new RequestFraudDetection($this->transactionId->toString(), $this->tokens);
    }

    public function afterApprovedFraudDetection(TransactionApproved $event, Message $message): void
    {
        $this->commands[] = new WithdrawTokens(walletId: $this->debtorWalletId, tokens: $this->tokens, description: $this->description, transactionId: $this->transactionId);
    }

    public function afterTokensWithdrawn(TokensWithdrawn $tokensWithdrawn, Message $message): void
    {
        $this->commands[] = new DepositTokens(walletId: $this->receivingWalletId, tokens: $this->tokens, description: $this->description, transactionId: $this->transactionId);
    }

    public function releaseCommands(): void
    {
        foreach ($this->commands as $command) {
            $this->commandBus->handle($command);
        }
        $this->commands = [];
    }

    public function toPayload(): array
    {
        return [
            'transaction_id' => $this->transactionId->toString(),
            'debtor_wallet_id' => $this->debtorWalletId->toString(),
            'receiving_wallet_id' => $this->receivingWalletId->toString(),
            'tokens' => $this->tokens,
            'description' => $this->description,
        ];
    }

    public function fromPayload(array $payload): void
    {
        $this->transactionId = TransactionId::fromString($payload['transaction_id']);
        $this->debtorWalletId = WalletId::fromString($payload['debtor_wallet_id']);
        $this->receivingWalletId = WalletId::fromString($payload['receiving_wallet_id']);
        $this->tokens = $payload['tokens'];
        $this->description = $payload['description'];
    }

    public function handleMethodInflector(): HandleMethodInflector
    {
        return new InflectHandlerMethodsFromType();
    }

    public function getProcessId(): string
    {
        return $this->transactionId->toString();
    }
}
