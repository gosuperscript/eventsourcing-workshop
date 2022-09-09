<?php

namespace Workshop\Domains\Wallet\Tests;

use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Reactors\HighBalanceReactor;
use Workshop\Domains\Wallet\WalletId;

class HighBalanceReactorTest extends MessageConsumerTestCase
{
    private InMemoryNotificationService $notificationService;
    private InMemoryHighBalanceRepository $highBalanceRepository;

    /** @test */
    public function it_sends_a_notification_when_balance_goes_over_100()
    {
        $walletId = WalletId::generate();
        $this->given()
            ->givenNextMessagesHaveAggregateRootIdOf($walletId)
            ->when(new TokensDeposited(101))
            ->then(function () use ($walletId) {
                $this->assertTrue($this->notificationService->notificationSendExactlyOnceForWallet($walletId));
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        $this->notificationService = new InMemoryNotificationService();
        $this->highBalanceRepository = new InMemoryHighBalanceRepository();
        return new HighBalanceReactor(
            $this->notificationService,
            $this->highBalanceRepository
        );
    }
}
