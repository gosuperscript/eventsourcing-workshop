<?php

namespace Workshop\Domains\Accounts\Commands;

final class OpenAccount
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $email,
        public readonly string $name,
    )
    {
    }
}
