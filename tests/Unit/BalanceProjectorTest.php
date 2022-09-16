<?php

namespace Workshop\Domains\Wallet\Tests;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\Wallet\WalletId;
use EventSauce\EventSourcing\MessageConsumer;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;

class BalanceProjectorTest extends MessageConsumerTestCase
{
    private WalletId $walletId;
    private InMemoryTransactionsRepository $transactionsRepository;
    
    public function setUp(): void
    {
        $this->walletId = WalletId::generate();
        parent::setUp();
    }

    public function messageConsumer(): MessageConsumer
    {
        $this->transactionsRepository = new InMemoryTransactionsRepository();
        return new TransactionsProjector($this->transactionsRepository);
    }

    /** @test */
    public function it_adds_a_transaction_to_the_transactions_on_deposit()
    {
        $this
            // This ensures that the messages all have an aggregate root id
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            // when some event happens
            ->when(
                // Because we want to use EventId and TimeOfRecording in our projection, we need to add those headers to the m
                // When we wouldn't need those, we could just use the `TokensDeposited` event directly. 
                // There might be a nice PR opportunity to make adding headers in this test easier. 

                (new Message(
                    new TokensDeposited(10)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            // we expect the following to happen
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
            // This ensures that the messages all have an aggregate root id
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            // when some event happens
            ->when(
                // Because we want to use EventId and TimeOfRecording in our projection, we need to add those headers to the m
                // When we wouldn't need those, we could just use the `TokensDeposited` event directly. 
                // There might be a nice PR opportunity to make adding headers in this test easier. 

                (new Message(
                    new TokensWithdrawn(10)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            // we expect the following to happen
            ->then(function (){
                $transactions = $this->transactionsRepository->getTransactions();
                $this->assertCount(1, $transactions);
                $transaction = $transactions[0];
                $this->assertEquals(-10, $transaction['amount']);
                $this->assertEquals($this->walletId->toString(), $transaction['walletId']);
            });
    }
}