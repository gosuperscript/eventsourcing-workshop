<?php

namespace Workshop\Domains\Accounts\Tests;

use PHPUnit\Framework\TestCase;
use Workshop\Domains\Accounts\Account;
use Workshop\Domains\Accounts\AccountState;
use Workshop\Domains\Accounts\Events\AccountVerified;

abstract class AccountTestCase extends TestCase
{
    protected string $accountId;
    private AccountState $state;
    private Account $account;
    private array $events;

    public function setUp(): void
    {
        $this->accountId = 'foo';
    }

    protected function given(AccountState $state)
    {
        $this->state = $state;
    }

    protected function when(\Closure $closure)
    {
        $this->account = Account::fromState($this->state);
        $closure($this->account);
    }

    protected function then(\Closure $closure)
    {
        $closure($this->account->getState());
    }

    protected function assertDispatched(AccountVerified $event)
    {
        if(!isset($this->events)){
            $this->events = $this->account->getUnpublishedEvents();
        }
        $this->assertTrue(in_array($event, $this->events));
    }


}
