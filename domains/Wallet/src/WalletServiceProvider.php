<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\ExplicitlyMappedClassNameInflector;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use EventSauce\UuidEncoding\StringUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\Tests\IntegrationTestMessageDispatcher;

class WalletServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(ExplicitlyMappedClassNameInflector::class, function (){
            return new ExplicitlyMappedClassNameInflector(config('eventsourcing.class_map'));
        });

        $this->app->bind(WalletMessageRepository::class, function (Application $application){
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(
                    classNameInflector: $this->app->make(ExplicitlyMappedClassNameInflector::class)
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
                ),
                new MessageDecoratorChain(
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
