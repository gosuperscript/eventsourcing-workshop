<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

class Wallet implements AggregateRoot
{
    use AggregateRootBehaviour;

    public function deposit(int $amountOfTokens): void
    {
        $this->recordThat(new TokensDeposited($amountOfTokens));
    }

    protected function applyTokensDeposited(TokensDeposited $event): void
    {

    }
}
