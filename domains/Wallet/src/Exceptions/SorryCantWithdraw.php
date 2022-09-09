<?php

namespace Workshop\Domains\Wallet\Exceptions;

use DomainException;

class SorryCantWithdraw extends DomainException
{
    public static function becauseOfInsufficientBalance(int $balance, int $amountOfTokens): self
    {
        return new self(sprintf(
            'Tried to withdraw %d tokens, but only %d tokens are available',
            $amountOfTokens,
            $balance
        ));
    }
}