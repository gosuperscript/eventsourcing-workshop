<?php

namespace Workshop\Domains\Wallet;

use EventSauce\Clock\SystemClock;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Robertbaelde\PersistingMessageBus\DefaultMessageDecorator;
use Robertbaelde\PersistingMessageBus\Laravel\IlluminateMessageRepository;
use Robertbaelde\PersistingMessageBus\MessageBus;
use Robertbaelde\PersistingMessageBus\MessageDispatcher;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\PublicEvents\Balance\Balance;

class WalletServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(WalletMessageRepository::class, function (Application $application){
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new BinaryUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () {
            return new WalletRepository(
                $this->app->make(WalletMessageRepository::class),
                new MessageDispatcherChain(),
                new DefaultHeadersDecorator(),
                new DotSeparatedSnakeCaseInflector(),
            );
        });

        $this->app->bind('WalletPublicEvents', function (Application $application) {
            return new IlluminateMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_public_events',
                tableSchema: new \Robertbaelde\PersistingMessageBus\DefaultTableSchema()
            );
        });

        $this->app->bind(MessageDispatcher::class, function (Application $application) {
            return new MessageDispatcher(
                messageBus: new MessageBus(
                    new Balance(),
                    new IlluminateMessageRepository(
                        connection: $application->make(DatabaseManager::class)->connection(),
                        tableName: 'wallet_public_events',
                        tableSchema: new \Robertbaelde\PersistingMessageBus\DefaultTableSchema()
                    )
                ),
                messageDecorator: new DefaultMessageDecorator(new SystemClock()),
            );
        });
    }
}
