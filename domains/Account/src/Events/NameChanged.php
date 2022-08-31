<?php

namespace Workshop\Domains\Accounts\Events;

class NameChanged
{

    public function __construct(public readonly string $accountId, public readonly string $name)
    {
    }
}
