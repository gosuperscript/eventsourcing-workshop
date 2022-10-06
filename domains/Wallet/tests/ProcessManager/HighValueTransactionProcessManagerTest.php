<?php

namespace Workshop\Domains\Wallet\Tests\ProcessManager;

use Carbon\Carbon;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use League\Tactician\CommandBus;
use Workshop\Domains\FraudDetection\Commands\RequestFraudDetection;
use Workshop\Domains\FraudDetection\Events\TransactionApproved;
use Workshop\Domains\ProcessManager\ProcessHeaders;
use Workshop\Domains\ProcessManager\ProcessManager;
use Workshop\Domains\ProcessManager\ProcessManagerTestCase;
use Workshop\Domains\Wallet\Commands\DepositTokens;
use Workshop\Domains\Wallet\Commands\WithdrawTokens;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\TransferInitiated;
use Workshop\Domains\Wallet\Transactions\DefaultTransactionProcessManager;
use Workshop\Domains\Wallet\Transactions\HighValueTransactionProcessManager;
use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\WalletId;

class HighValueTransactionProcessManagerTest extends ProcessManagerTestCase
{
    private TransactionId $transactionId;
    private WalletId $debtorWalletId;
    private WalletId $creditWalletId;
    private string $description;
    private CommandBus $commandBus;

    public function setUp(): void
    {
        $this->transactionId = TransactionId::generate();
        $this->debtorWalletId = WalletId::generate();
        $this->creditWalletId = WalletId::generate();
        $this->description = 'test';
        $this->commandBus = new FakeCommandBus();
        parent::setUp();
    }

    /** @test */
    public function it_should_start_on_transfer_initiated_with_amount_greater_or_equals_to_100()
    {
        $transferInitiatedMessage = $this->getTransferInitiatedMessage(100);
        $this->assertTrue($this->processManager->startsOn($transferInitiatedMessage));
    }

    /** @test */
    public function it_should_not_start_on_transfer_initiated_with_amount_lesser_than_100()
    {
        $transferInitiatedMessage = $this->getTransferInitiatedMessage(99);
        $this->assertFalse($this->processManager->startsOn($transferInitiatedMessage));
    }

    /** @test */
    public function it_will_request_fraud_detection_on_transfer_initiated()
    {
        $this
            ->given()
            ->when($this->getTransferInitiatedMessage($tokens = 100))
            ->then(function () use ($tokens) {
                $this->commandBus->assertDispatched(
                    new RequestFraudDetection(
                        transactionId: $this->transactionId->toString(),
                        tokens: $tokens,
                    ),
                );
            });
    }

    /** @test */
    public function it_will_withdraw_tokens_after_transaction_approved()
    {
        $this
            ->given($this->getTransferInitiatedMessage($tokens = 100))
            ->when($this->getTransactionApproved())
            ->then(function () use ($tokens) {
                $this->commandBus->assertDispatched(
                    new WithdrawTokens(walletId: $this->debtorWalletId, tokens: $tokens, description: $this->description, transactionId: $this->transactionId)
                );
            });
    }

    /** @test */
    public function it_will_deposit_tokens_after_tokens_withdrawn()
    {
        $this
            ->given($this->getTransferInitiatedMessage($tokens = 100))
            ->when($this->getTokensWithdrawn($tokens))
            ->then(function () use ($tokens) {
                $this->commandBus->assertDispatched(
                    new DepositTokens(walletId: $this->creditWalletId, tokens: $tokens, description: $this->description, transactionId: $this->transactionId)
                );
            });
    }

    protected function processManager(): ProcessManager
    {
        return new HighValueTransactionProcessManager($this->commandBus);
    }

    private function getTransferInitiatedMessage(int $tokens): Message
    {
        return (new Message(
            new TransferInitiated(
                transactionId: $this->transactionId,
                receivingWalletId: $this->creditWalletId,
                tokens: $tokens,
                description: $this->description,
                startedAt: Carbon::now(),
            ),
        ))->withHeaders([
            ProcessHeaders::CORRELATION_ID => $this->transactionId->toString(),
            Header::AGGREGATE_ROOT_ID => $this->debtorWalletId,
        ]);
    }

    private function getTokensWithdrawn(int $tokens): Message
    {
        return (new Message(
            new TokensWithdrawn(
                tokens: $tokens,
                transactedAt: Carbon::now(),
                transactionId: $this->transactionId,
                description: $this->description
            ),
        ))->withHeaders([
            ProcessHeaders::CORRELATION_ID => $this->transactionId->toString(),
            Header::AGGREGATE_ROOT_ID => $this->debtorWalletId,
        ]);
    }

    private function getTransactionApproved()
    {
        return (new Message(
            new TransactionApproved(
                transactionId: $this->transactionId->toString(),
            )
        ))->withHeaders([
            ProcessHeaders::CORRELATION_ID => $this->transactionId->toString(),
            Header::AGGREGATE_ROOT_ID => $this->debtorWalletId,
        ]);
    }
}
