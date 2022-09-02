<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\ExplicitlyMappedClassNameInflector;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\ObjectMapperPayloadSerializer;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Decorators\RandomNumberHeaderDecorator;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;

class WalletServiceProvider extends ServiceProvider
{

    public function register()
    {
        // This should live in a config file.
        $classNameInflector = new ExplicitlyMappedClassNameInflector(config('eventsourcing.class_map'));

        $this->app->bind(WalletMessageRepository::class, function (Application $application) use ($classNameInflector) {
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(classNameInflector: $classNameInflector, payloadSerializer: new ObjectMapperPayloadSerializer()),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new BinaryUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () use ($classNameInflector) {
            return new WalletRepository(
                messageRepository: $this->app->make(WalletMessageRepository::class),
                dispatcher: new MessageDispatcherChain(),
                decorator: new MessageDecoratorChain(
                    new DefaultHeadersDecorator(inflector: $classNameInflector),
                    new RandomNumberHeaderDecorator()
                ),
                classNameInflector: $classNameInflector,
            );
        });
    }
}
