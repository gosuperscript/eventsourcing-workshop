<?php

namespace Workshop\Domains\Wallet\Tests\Transactions;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use League\Tactician\CommandBus;
use Workshop\Domains\ProcessManager\ProcessHeaders;
use Workshop\Domains\ProcessManager\ProcessManager;
use Workshop\Domains\ProcessManager\ProcessManagerTestCase;
use Workshop\Domains\Wallet\Commands\DepositTokens;
use Workshop\Domains\Wallet\Commands\WithdrawTokens;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\TransferInitiated;
use Workshop\Domains\Wallet\Transactions\DefaultTransactionProcessManager;
use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\WalletId;

class DefaultTransactionProcessManagerTest extends ProcessManagerTestCase
{
    private TransactionId $transactionId;
    private WalletId $debtorWalletId;
    private WalletId $creditWalletId;
    private string $description;
    private int $tokens;
    private CommandBus $commandBus;

    public function setUp(): void
    {
        $this->transactionId = TransactionId::generate();
        $this->debtorWalletId = WalletId::generate();
        $this->creditWalletId = WalletId::generate();
        $this->description = 'test';
        $this->tokens = 99;
        $this->commandBus = new FakeCommandBus();
        parent::setUp();
    }

    /** @test */
    public function it_should_start_on_transfer_initiated()
    {
        $transferInitiatedMessage = $this->getTransferInitiatedMessage();
        $this->assertTrue($this->processManager->startsOn($transferInitiatedMessage));
    }

    /** @test */
    public function it_will_withdraw_tokens_on_transfer_initiated()
    {
        $this
            ->given()
            ->when($this->getTransferInitiatedMessage())
            ->then(function (){
                $this->commandBus->assertDispatched(
                    new WithdrawTokens(walletId: $this->debtorWalletId, tokens: $this->tokens, description: $this->description, transactionId: $this->transactionId)
                );
            });
    }

    /** @test */
    public function it_will_deposit_tokens_after_tokens_withdrawn()
    {
        $this
            ->given($this->getTransferInitiatedMessage())
            ->when($this->getTokensWithdrawn())
            ->then(function (){
                $this->commandBus->assertDispatched(
                    new DepositTokens(walletId: $this->creditWalletId, tokens: $this->tokens, description: $this->description, transactionId: $this->transactionId)
                );
            });
    }

    protected function processManager(): ProcessManager
    {
        return new DefaultTransactionProcessManager($this->commandBus);
    }

    private function getTransferInitiatedMessage(): Message
    {
        return (new Message(
            new TransferInitiated(
                transactionId: $this->transactionId,
                receivingWalletId: $this->creditWalletId,
                tokens: $this->tokens,
                description: $this->description,
                startedAt: new \DateTimeImmutable()
            ),
        ))->withHeaders([
            ProcessHeaders::CORRELATION_ID => $this->transactionId->toString(),
            Header::AGGREGATE_ROOT_ID => $this->debtorWalletId,
        ]);
    }

    private function getTokensWithdrawn(): Message
    {
        return (new Message(
            new TokensWithdrawn(
                tokens: $this->tokens,
                description: $this->description,
                transactedAt: new \DateTimeImmutable(),
                transactionId: $this->transactionId
            ),
        ))->withHeaders([
            ProcessHeaders::CORRELATION_ID => $this->transactionId->toString(),
            Header::AGGREGATE_ROOT_ID => $this->debtorWalletId,
        ]);
    }


}
