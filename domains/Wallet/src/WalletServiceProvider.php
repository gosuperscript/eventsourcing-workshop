<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\StringUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use EventSauce\EventSourcing\Serialization\ObjectMapperPayloadSerializer;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use EventSauce\EventSourcing\Upcasting\UpcasterChain;
use Workshop\Domains\Wallet\Infra\EloquentNotificationService;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;
use Workshop\Domains\Wallet\Infra\EloquentTransactionsReadModelRepository;
use Workshop\Domains\Wallet\Infra\NotificationService;
use Workshop\Domains\Wallet\Upcasters\TransactedAtUpcaster;
use Workshop\Domains\Wallet\Reactors\NotificationsReactor;
use Workshop\Domains\Wallet\PublicEvents\Balance\BalanceTopic;
use EventSauce\EventSourcing\Upcasting\UpcastingMessageSerializer;
use EventSauce\MessageRepository\IlluminateMessageRepository\IlluminateUuidV4MessageRepository;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\Clock\SystemClock;

class WalletServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(WalletMessageRepository::class, function (Application $application){
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new UpcastingMessageSerializer(
                    eventSerializer: new ConstructingMessageSerializer(
                        payloadSerializer: new ObjectMapperPayloadSerializer()
                    ),
                    upcaster: new UpcasterChain(
                        upcasters: new TransactedAtUpcaster()
                    )
                ),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new StringUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () {
            return new WalletRepository(
                $this->app->make(WalletMessageRepository::class),
                new MessageDispatcherChain(
                    new SynchronousMessageDispatcher(
                        $this->app->make(TransactionsProjector::class),
                    ),
                    new SynchronousMessageDispatcher(
                        $this->app->make(NotificationsReactor::class)
                    )
                ),
                new DefaultHeadersDecorator(),
                new DotSeparatedSnakeCaseInflector(),
            );
        });

        $this->app->bind('WalletPublicEvents', function (Application $application) {
            return new \Robertbaelde\PersistingMessageBus\Laravel\IlluminateMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_public_events',
                tableSchema: new \Robertbaelde\PersistingMessageBus\DefaultTableSchema(),
            );
        });

        $this->app->bind(MessageDispatcher::class, function (Application $application) {
            return new MessageDispatcher(
                messageBus: new \Robertbaelde\PersistingMessageBus\MessageBus(
                    new BalanceTopic(),
                    new \Robertbaelde\PersistingMessageBus\Laravel\IlluminateMessageRepository(
                        connection: $application->make(DatabaseManager::class)->connection(),
                        tableName: 'wallet_public_events',
                        tableSchema: new \Robertbaelde\PersistingMessageBus\DefaultTableSchema()
                    )
                ),
                messageDecorator: new \Robertbaelde\PersistingMessageBus\DefaultMessageDecorator(new SystemClock()),
            );
        });

        $this->app->bind(TransactionsReadModelRepository::class, EloquentTransactionsReadModelRepository::class);
        $this->app->bind(NotificationService::class, EloquentNotificationService::class);
    }
}
