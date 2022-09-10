<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalRefused;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;

class Wallet implements AggregateRoot
{
    use AggregateRootBehaviour;

    private int $balance = 0;

    public function deposit(int $amountOfTokens): void
    {
        $this->recordThat(new TokensDeposited($amountOfTokens));
    }

    public function withdraw(int $amountOfTokens): void
    {
        if ($amountOfTokens > $this->balance) {
            $this->recordThat(
                new WithdrawalRefused($amountOfTokens)
            );
            throw SorryCantWithdraw::becauseOfInsufficientBalance($this->balance, $amountOfTokens);
        } else {
            $this->recordThat(
                new TokensWithdrawn($amountOfTokens)
            );
        }
    }

    protected function applyTokensDeposited(TokensDeposited $event): void
    {
        $this->balance += $event->amountOfTokens();
    }

    protected function applyTokensWithdrawn(TokensWithdrawn $event): void
    {
        $this->balance -= $event->amountOfTokens();
    }

    protected function applyWithdrawalRefused(WithdrawalRefused $event): void
    {
    }
}
