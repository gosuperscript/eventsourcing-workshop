<?php

namespace Workshop\Domains\Accounts;

use Workshop\Domains\Accounts\Events\AccountOpened;
use Workshop\Domains\Accounts\Events\AccountVerified;
use Workshop\Domains\Accounts\Exceptions\SorryCantVerifyAccount;

final class Account
{
    private array $unpublishedEvents = [];
    public readonly string $id;

    private function __construct(
        private AccountState $state
    )
    {
        $this->id = $state->accountId;
    }

    public static function fromState(AccountState $state): self
    {
        return new self($state);
    }

    public function getState(): AccountState
    {
        return $this->state;
    }

    public function getUnpublishedEvents(): array
    {
        return $this->unpublishedEvents;
    }

    public static function open(Commands\OpenAccount $openAccount): self
    {
        $account = new self(new AccountState($openAccount->accountId));
        $account->state->name = $openAccount->name;
        $account->state->email = $openAccount->email;
        $account->publish(AccountOpened::fromCommand($openAccount));
        return $account;
    }

    public function changeName(string $name)
    {
        $this->state->name = $name;
        $this->publish(new Events\NameChanged($this->state->accountId, $name));
    }

    /**
     * @throws SorryCantVerifyAccount
     */
    public function verify(): void
    {
        if($this->state->verified){
            throw SorryCantVerifyAccount::alreadyVerified();
        }
        $this->state->verified = true;
        $this->publish(new AccountVerified($this->state->accountId));
    }

    private function publish(object $event)
    {
        $this->unpublishedEvents[] = $event;
    }
}
