<?php

namespace Workshop\Domains\Wallet\Exceptions;

class SorryCantWithdraw extends \Exception
{
    public function __construct($message = "Can't withdraw", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
