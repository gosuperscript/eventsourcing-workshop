<?php

namespace Workshop\Domains\Accounts\Events;

use Workshop\Domains\Accounts\Commands\OpenAccount;

final class AccountOpened
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $name,
        public readonly string $email
    )
    {
    }

    public static function fromCommand(OpenAccount $openAccount): self
    {
        return new self($openAccount->accountId, $openAccount->name, $openAccount->email);
    }
}
