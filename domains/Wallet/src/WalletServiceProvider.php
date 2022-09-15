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
use EventSauce\UuidEncoding\StringUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Decorators\EventIDDecorator;
use Workshop\Domains\Wallet\Decorators\RandomNumberMessageHeaderAdder;
use Workshop\Domains\Wallet\Infra\EloquentBalanceRepository;
use Workshop\Domains\Wallet\Infra\EloquentTransactionsRepository;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\Projections\BalanceProjector;
use Workshop\Domains\Wallet\Projections\TransactionsProjector;
use Workshop\Domains\Wallet\Tests\Utilities\IntegrationTestMessageDispatcher;

class WalletServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(ExplicitlyMappedClassNameInflector::class, function (){
            return new ExplicitlyMappedClassNameInflector(config('eventsourcing.class_map'));
        });

        $this->app->bind(TransactionsProjector::class, function (){
            return new TransactionsProjector(new EloquentTransactionsRepository());
        });
        $this->app->bind(BalanceProjector::class, function (){
            return new BalanceProjector(new EloquentBalanceRepository());
        });

        $this->app->bind(WalletMessageRepository::class, function (Application $application){
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(
                    classNameInflector: $this->app->make(ExplicitlyMappedClassNameInflector::class),
                    payloadSerializer: new ObjectMapperPayloadSerializer(),
                ),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new StringUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () {
            return new WalletRepository(
                $this->app->make(WalletMessageRepository::class),
                new MessageDispatcherChain(
                    IntegrationTestMessageDispatcher::instance(),
                    new SynchronousMessageDispatcher(
                        $this->app->make(TransactionsProjector::class),
                        $this->app->make(BalanceProjector::class),
                    )
                ),
                new MessageDecoratorChain(
                    new EventIDDecorator(),
                    new DefaultHeadersDecorator(
                        inflector: $this->app->make(ExplicitlyMappedClassNameInflector::class),
                    ),
                    new RandomNumberMessageHeaderAdder(),
                ),
                $this->app->make(ExplicitlyMappedClassNameInflector::class),
            );
        });
    }
}
