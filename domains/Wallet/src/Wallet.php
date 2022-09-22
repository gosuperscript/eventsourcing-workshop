<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalFailed;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;

class Wallet implements AggregateRoot
{
    use AggregateRootBehaviour;

    private int $balance = 0;

    public function deposit(int $tokens, string $description, \DateTimeImmutable $transactedAt): void
    {
        $this->recordThat(new TokensDeposited($tokens, $description, $transactedAt, $this->balance + $tokens));
    }

    public function withdraw(int $tokens, string $description, \DateTimeImmutable $transactedAt)
    {
        if($this->balance < $tokens) {
            $this->recordThat(WithdrawalFailed::becauseOfInsufficientFunds());
            throw SorryCantWithdraw::becauseOfInsufficientFunds();
        }
        $this->recordThat(new TokensWithdrawn($tokens, $description, $transactedAt, $this->balance - $tokens));
    }

    private function applyTokensDeposited(TokensDeposited $event): void
    {
        $this->balance = $event->balance;
    }

    private function applyTokensWithdrawn(TokensWithdrawn $event): void
    {
        $this->balance = $event->balance;
    }

    private function applyWithdrawalFailed(WithdrawalFailed $event): void
    {
    }
}
