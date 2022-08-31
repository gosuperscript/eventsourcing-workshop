<?php

namespace Workshop\Domains\Accounts\Events;

class AccountVerified
{

    public function __construct(public readonly string $accountId)
    {
    }
}
