<?php

namespace Workshop\Domains\Wallet\Tests;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use Workshop\Domains\Wallet\WalletId;

class TransactionsProjectorTest extends MessageConsumerTestCase
{

    private WalletId $walletId;
    private InMemoryTransactionsRepository $transactionsRepository;

    public function setUp(): void
    {
        $this->walletId = WalletId::generate();
        parent::setUp();
    }

    /** @test */
    public function it_adds_a_transaction_to_the_transactions_on_deposit()
    {
        $this
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->when(
                (new Message(
                    new TokensDeposited(10)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            ->then(function (){
                $transactions = $this->transactionsRepository->getTransactions();
                $this->assertCount(1, $transactions);
                $transaction = $transactions[0];
                $this->assertEquals(10, $transaction['amount']);
                $this->assertEquals($this->walletId->toString(), $transaction['walletId']);
            });
    }

    /** @test */
    public function it_adds_a_transaction_to_the_transactions_on_withdrawal()
    {
        $this
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->when(
                (new Message(
                    new TokensWithdrawn(10)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            ->then(function (){
                $transactions = $this->transactionsRepository->getTransactions();
                $this->assertCount(1, $transactions);
                $transaction = $transactions[0];
                $this->assertEquals(-10, $transaction['amount']);
                $this->assertEquals($this->walletId->toString(), $transaction['walletId']);
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        $this->transactionsRepository = new InMemoryTransactionsRepository();
        return new TransactionsProjector($this->transactionsRepository);
    }
}
