<?php

namespace Workshop\Domains\Wallet\Tests\Unit;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Projections\BalanceProjector;
use Workshop\Domains\Wallet\Tests\Utilities\InMemoryBalanceRepository;
use Workshop\Domains\Wallet\WalletId;

class BalanceProjectorTest extends MessageConsumerTestCase
{
    private WalletId $walletId;
    private InMemoryBalanceRepository $repository;

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
                $balance = $this->repository->getBalanceFor($this->walletId);
                $this->assertEquals(10, $balance->balance);
            });
    }

    /** @test */
    public function it_adds_a_transaction_to_the_transactions_on_withdrawal()
    {
        $this
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->given(
                (new Message(
                    new TokensDeposited(10)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id1',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ]))
            ->when(
                (new Message(
                    new TokensWithdrawn(5)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id2',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:36.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            ->then(function (){
                $balance = $this->repository->getBalanceFor($this->walletId);
                $this->assertEquals(5, $balance->balance);
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        $this->repository = new InMemoryBalanceRepository();
        return new BalanceProjector($this->repository);
    }
}