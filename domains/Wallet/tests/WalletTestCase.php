<?php

namespace Workshop\Domains\Wallet\Tests;

use DateTimeImmutable;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

abstract class WalletTestCase extends AggregateRootTestCase
{
    protected DateTimeImmutable $now;

    public function setUp(): void
    {
        parent::setUp();
        $this->now = new DateTimeImmutable('2022-01-01 00:00:00');
    }

    protected function givenTokensDeposited(int $tokens, $description = 'foo', $now = null): self
    {
        return $this->given(new TokensDeposited($tokens, $description, $now ?? $this->now));
    }

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

}
