<?php

namespace Workshop\Domains\Accounts\Query;

use Workshop\Domains\Accounts\AccountState;

class Account
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $verified,
    )
    {
    }

    public static function fromState(AccountState $state): self
    {
        return new self($state->accountId, $state->name, $state->email, $state->verified);
    }
}
