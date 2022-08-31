<?php

namespace Workshop\Domains\Accounts\Exceptions;

class SorryCantVerifyAccount extends \Exception
{

    public static function alreadyVerified(): self
    {
        return new self('Sorry, you can\'t verify an account twice.');
    }
}
