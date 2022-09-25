<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Exception;
use Workshop\Domains\Wallet\Events;
use Workshop\Domains\Wallet\Exceptions;

class Wallet implements AggregateRoot
{
    use AggregateRootBehaviour;

    public int $tokens = 0;

    public function deposit(int $tokens)
    {
        $this->recordThat(new Events\TokensDeposited($tokens));

        return $this;
    }

    private function applyTokensDeposited(Events\TokensDeposited $event): void
    {
        $this->tokens += $event->tokens();
    }

    public function withdraw(int $tokens)
    {
        if ($this->tokens >= $tokens) {
            $this->recordThat(new Events\TokensWithdrawn($tokens));
        } else {
            $this->recordThat(new Events\TokensOverdrawAttempted(time()));
        }

        return $this;
    }

    private function applyTokensWithdrawn(Events\TokensWithdrawn $event): void
    {
        $this->tokens -= $event->tokens();
    }

    private function applyTokensOverdrawAttempted(Events\TokensOverdrawAttempted $event): void
    {
        
    }
}
