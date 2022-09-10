<?php

namespace Workshop\Domains\Wallet\Events;

class WithdrawalRefused
{
    private int $amountOfTokens;

    public function __construct(int $amountOfTokens)
    {
        $this->amountOfTokens = $amountOfTokens;
    }

    public function amountOfTokens(): int
    {
        return $this->amountOfTokens;
    }
}