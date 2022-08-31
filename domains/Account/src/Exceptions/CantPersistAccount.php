<?php

namespace Workshop\Domains\Accounts\Exceptions;

class CantPersistAccount extends \Exception
{

    public static function accountChangedByOtherProcess(): self
    {
        return new self('Account changed by other process');
    }
}
