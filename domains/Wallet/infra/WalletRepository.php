<?php

namespace Workshop\Domains\Wallet\Infra;

use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\Decorators;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\MessageDecoratorChain;

/** @method \Workshop\Domains\Wallet\Wallet retrieve(\Workshop\Domains\Wallet\WalletId $aggregateRootId) */
class WalletRepository extends EventSourcedAggregateRootRepository
{
    public function __construct(
        MessageRepository $messageRepository,
        MessageDispatcher $dispatcher,
        MessageDecorator $decorator,
        ClassNameInflector $classNameInflector,
    ) {
        parent::__construct(
            aggregateRootClassName: Wallet::class,
            messageRepository: $messageRepository,
            dispatcher: $dispatcher,
            decorator: new MessageDecoratorChain(
                new Decorators\EventIDDecorator(),
                new DefaultHeadersDecorator(inflector: $classNameInflector),
                new Decorators\RandomNumberHeaderDecorator()
            ),
            classNameInflector: $classNameInflector
        );
    }
}
