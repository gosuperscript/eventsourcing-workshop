<?php

namespace Workshop\Domains\Wallet\Tests;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

abstract class WalletTestCase extends AggregateRootTestCase
{
    protected function newAggregateRootId(): AggregateRootId
    {
        return WalletId::generate();
    }

    /**
     * @phpstan-return class-string<AggregateRoot>
     */
    protected function aggregateRootClassName(): string
    {
        return Wallet::class;
    }

    public function handle(\Closure $closure)
    {
        /** @var Wallet $wallet */
        $wallet = $this->repository->retrieve($this->aggregateRootId);
        try {
            $closure($wallet);
        } finally {
            $this->repository->persist($wallet);
        }
    }

    protected function messageDispatcher(): MessageDispatcher
    {
        return new SynchronousMessageDispatcher();
    }
}
