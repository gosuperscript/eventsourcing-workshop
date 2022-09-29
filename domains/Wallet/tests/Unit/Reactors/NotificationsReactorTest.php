<?php

namespace Workshop\Domains\Wallet\Tests\Unit\Reactors;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Reactors\NotificationsReactor;
use Workshop\Domains\Wallet\Tests\InMemoryNotificationService;
use Workshop\Domains\Wallet\WalletId;
use Tests\CreatesApplication;
use Illuminate\Contracts\Console\Kernel;

class NotificationsReactorTest extends MessageConsumerTestCase
{
    use CreatesApplication;
    
    private WalletId $walletId;
    private InMemoryNotificationService $notificationService;

    public static function setUpBeforeClass(): void
    {
        $app = require __DIR__.'/../../../../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();
    }

    public function setUp(): void
    {
        $this->walletId = WalletId::generate();
        
        parent::setUp();
    }

    public function test_it_does_not_trigger_notification_if_not_reaching_threshold()
    {
        $this->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->when(
                (new Message(
                    new TokensDeposited(99)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            ->then(function (){
                $this->assertEmpty($this->notificationService->getNotifications());
            });
    }

    public function test_it_does_trigger_notification_if_reaching_threshold()
    {
        $this->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->when(
                (new Message(
                    new TokensDeposited(101)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            ->then(function (){
                $this->assertNotEmpty($this->notificationService->getNotifications());
            });
    }

    public function test_it_does_not_trigger_notification_after_reaching_threshold()
    {
        $this->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->given((new Message(
                new TokensDeposited(101)
            ))->withHeaders([
                Header::EVENT_ID => 'event-id',
                Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
            ]))
            ->when(
                (new Message(
                    new TokensDeposited(101)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            ->then(function (){
                $this->assertTrue($this->notificationService->notificationSendExactlyOnceForWallet($this->walletId));
            });
    }

    public function test_it_does_trigger_notification_if_reaching_threshold_second_time()
    {
        $this->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->given((new Message(
                new TokensDeposited(101)
            ))->withHeaders([
                Header::EVENT_ID => 'event-id',
                Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
            ]), (new Message(
                new TokensWithdrawn(101)
            ))->withHeaders([
                Header::EVENT_ID => 'event-id',
                Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
            ]))
            ->when(
                (new Message(
                    new TokensDeposited(102)
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:36.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO'
                ])
            )
            ->then(function (){
                $this->assertEquals(2, count($this->notificationService->getNotifications()));
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        // we use an in memory repository for this test. So its fast, and isn't dependent on the database.
        $this->notificationService = new InMemoryNotificationService();

        return new NotificationsReactor($this->notificationService);
    }
}