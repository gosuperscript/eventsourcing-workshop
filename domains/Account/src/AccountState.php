<?php

namespace Workshop\Domains\Accounts;

final class AccountState
{
    public function __construct(
        public readonly string $accountId,
        public readonly int $version = 0,
        public ?string $email = null,
        public ?string $name = null,
        public bool $verified = false,
    )
    {
    }

    public function incrementVersion(): self
    {
        return new self(
            $this->accountId,
            $this->version + 1,
            $this->email,
            $this->name,
            $this->verified
        );
    }
}
