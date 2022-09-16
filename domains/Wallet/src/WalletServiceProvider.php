<?php

namespace Workshop\Domains\Wallet;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\DatabaseManager;
use EventSauce\UuidEncoding\StringUuidEncoder;
use EventSauce\EventSourcing\MessageDecoratorChain;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use Workshop\Domains\Wallet\Decorators\EventIDDecorator;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use EventSauce\EventSourcing\ExplicitlyMappedClassNameInflector;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;
use Workshop\Domains\Wallet\Decorators\RandomNumberHeaderDecorator;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\ObjectMapperPayloadSerializer;
use Workshop\Domains\Wallet\Infra\EloquentTransactionsReadModelRepository;

class WalletServiceProvider extends ServiceProvider
{

    public function register()
    {
        $classNameInflector = new ExplicitlyMappedClassNameInflector(config('eventsourcing.class_map'));

        $this->app->bind(TransactionsReadModelRepository::class, EloquentTransactionsReadModelRepository::class);

        $this->app->bind(WalletMessageRepository::class, function (Application $application) use ($classNameInflector) {
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(classNameInflector: $classNameInflector, payloadSerializer: new ObjectMapperPayloadSerializer()),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new StringUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () use ($classNameInflector) {
            return new WalletRepository(
                messageRepository: $this->app->make(WalletMessageRepository::class),
                dispatcher: new MessageDispatcherChain(
                    new SynchronousMessageDispatcher(
                        $this->app->make(TransactionsProjector::class),
                    )
                ),
                decorator: new MessageDecoratorChain(
                    new EventIDDecorator(),
                    new DefaultHeadersDecorator(inflector: $classNameInflector),
                    new RandomNumberHeaderDecorator()
                ),
                classNameInflector: $classNameInflector,
            );
        });
    }
}
