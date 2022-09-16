<?php

namespace Workshop\Domains\Wallet\Exceptions;

class SorryCantWithdraw extends \Exception
{
    public static function becauseOfInsufficientFunds(): self
    {
        return new self('Sorry, you can\'t withdraw more than you have');
    }
}
