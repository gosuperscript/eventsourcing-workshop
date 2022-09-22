<?php

namespace Workshop\Domains\Accounts\Tests\Reactors;

use EventSauce\Clock\SystemClock;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Robertbaelde\PersistingMessageBus\DefaultMessageDecorator;
use Robertbaelde\PersistingMessageBus\MessageBus;
use Robertbaelde\PersistingMessageBus\MessageDispatcher;
use Robertbaelde\PersistingMessageBus\MessageRepository\IncrementalCursor;
use Robertbaelde\PersistingMessageBus\RawMessage;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Infra\InMemoryMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\PublicEvents\Balance\Balance;
use Workshop\Domains\Wallet\Reactors\BalancePublicEventReactor;
use Workshop\Domains\Wallet\Tests\InMemoryWalletBalanceRepository;
use Workshop\Domains\Wallet\WalletId;

class BalancePublicEventReactorTest extends MessageConsumerTestCase
{
    private WalletBalanceRepository $walletBalanceRepository;

    private InMemoryMessageRepository $messageRepository;

    /** @test */
    public function it_dispatches_public_balance_events()
    {
        $walletId = WalletId::generate();

        $this->walletBalanceRepository->updateWalletTokens(
            walletId: $walletId,
            tokens: $balance = 100,
        );

        $this->givenNextMessagesHaveAggregateRootIdOf($walletId)
            ->when(new TokensDeposited(
                tokens: 100,
                transacted_at: now(),
            ))->then(function () use ($balance) {
                /** @var RawMessage[] $messages */
                $messages = $this->messageRepository->getMessagesForTopic((new Balance())->getName(), new IncrementalCursor())->messages;

                $this->assertCount(1, $messages);

                $message = $messages[0];

                $this->assertEquals($balance, json_decode($message->messagePayload)->balance);
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        return new BalancePublicEventReactor(
            messageDispatcher: new MessageDispatcher(
                messageBus: new MessageBus(
                    topic: new Balance(),
                    messageRepository: $this->messageRepository = new InMemoryMessageRepository(),
                ),
                messageDecorator: new DefaultMessageDecorator(new SystemClock()),
            ),
            walletBalanceRepository: $this->walletBalanceRepository = new InMemoryWalletBalanceRepository(),
        );
    }
}
