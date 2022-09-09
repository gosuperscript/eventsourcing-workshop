<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

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
        $this->recordThat(
            $amountOfTokens > $this->balance
                ? new WithdrawalRefused($amountOfTokens)
                : new TokensWithdrawn($amountOfTokens)
        );
    }

    protected function applyTokensDeposited(TokensDeposited $event): void
    {
        $this->balance += $event->tokens();
    }

    protected function applyTokensWithdrawn(TokensWithdrawn $event): void
    {
        $this->balance -= $event->tokens();
    }

    protected function applyWithdrawalRefused(WithdrawalRefused $event): void
    {
    }
}
