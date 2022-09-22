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

    private int $tokens = 0;

    public function deposit(int $tokens, string $description)
    {
        $this->recordThat(new TokensDeposited($tokens, $description));
    }

    public function withdraw(int $tokens, string $description)
    {
        if($this->tokens < $tokens) {
            $this->recordThat(WithdrawalFailed::becauseOfInsufficientFunds());
            throw SorryCantWithdraw::becauseOfInsufficientFunds();
        }
        $this->recordThat(new TokensWithdrawn($tokens, $description));
    }

    private function applyTokensDeposited(TokensDeposited $event): void
    {
        $this->tokens += $event->tokens;
    }

    private function applyTokensWithdrawn(TokensWithdrawn $event): void
    {
        $this->tokens -= $event->tokens;
    }

    private function applyWithdrawalFailed(WithdrawalFailed $event): void
    {
    }
}
