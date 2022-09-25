<?php

namespace Workshop\Domains\Wallet\Infra;

use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use Workshop\Domains\Wallet\Wallet;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\ObjectMapperPayloadSerializer;

/** @method \Workshop\Domains\Wallet\Wallet retrieve(\Workshop\Domains\Wallet\WalletId $aggregateRootId) */
class WalletRepository extends EventSourcedAggregateRootRepository
{
    public function __construct(
        MessageRepository $messageRepository,
        MessageDispatcher $dispatcher,
        MessageDecorator $decorator,
        ClassNameInflector $classNameInflector,
    ) {
        $chain = new MessageDecoratorChain(
            new DefaultHeadersDecorator(),
            new WalletDecorator()
        );

        parent::__construct(
            aggregateRootClassName: Wallet::class,
            messageRepository: $messageRepository,
            dispatcher: $dispatcher,
            decorator: $chain,
            classNameInflector: $classNameInflector
        );
    }
}
