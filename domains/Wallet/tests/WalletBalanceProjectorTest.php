<?php

namespace Workshop\Domains\Wallet\Tests;

use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Projectors\WalletBalanceProjector;
use Workshop\Domains\Wallet\WalletId;

class WalletBalanceProjectorTest extends MessageConsumerTestCase
{
    private InMemoryWalletBalanceRepository $walletReadRepository;
    private WalletId $walletId;

    public function setUp(): void
    {
        parent::setUp();
        $this->walletId = WalletId::generate();
    }

    /** @test */
    public function it_increments_balance()
    {
        $this->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->when(new TokensDeposited(10, 'foo'))
            ->then(function (){
                $this->assertEquals(10, $this->walletReadRepository->getWalletTokens($this->walletId));
            });
    }

    /** @test */
    public function it_decrements_balance()
    {
        $this->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->given(new TokensDeposited(10, 'foo'))
            ->when(new TokensWithdrawn(5, 'foo'))
            ->then(function (){
                $this->assertEquals(5, $this->walletReadRepository->getWalletTokens($this->walletId));
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        $this->walletReadRepository = new InMemoryWalletBalanceRepository();
        return new WalletBalanceProjector($this->walletReadRepository);
    }
}
