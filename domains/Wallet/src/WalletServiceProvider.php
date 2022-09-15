<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\ExplicitlyMappedClassNameInflector;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\ObjectMapperPayloadSerializer;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Decorators\EventIDDecorator;
use Workshop\Domains\Wallet\Decorators\RandomNumberHeaderDecorator;
use Workshop\Domains\Wallet\Infra\EloquentTransactionsReadModelRepository;
use Workshop\Domains\Wallet\Infra\IlluminateWalletBalanceRepository;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use Workshop\Domains\Wallet\Projectors\WalletBalanceProjector;

class WalletServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->app->bind(TransactionsReadModelRepository::class, fn(Application $app) => new EloquentTransactionsReadModelRepository());
        $this->app->bind(WalletBalanceRepository::class, fn(Application $app) => new IlluminateWalletBalanceRepository());

        // This should live in a config file.
        $classNameInflector = new ExplicitlyMappedClassNameInflector(config('eventsourcing.class_map'));

        $this->app->bind(WalletMessageRepository::class, function (Application $application) use ($classNameInflector) {
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(classNameInflector: $classNameInflector),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new BinaryUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () use ($classNameInflector) {
            return new WalletRepository(
                messageRepository: $this->app->make(WalletMessageRepository::class),
                dispatcher: new MessageDispatcherChain(
                    new SynchronousMessageDispatcher(
                        $this->app->make(TransactionsProjector::class),
                        $this->app->make(WalletBalanceProjector::class),
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
