<?php

namespace Workshop\Domains\Accounts\Commands;

class VerifyAccount
{
    public function __construct(public readonly string $accountId)
    {

    }
}
