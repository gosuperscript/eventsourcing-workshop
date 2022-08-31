<?php

namespace Workshop\Domains\Accounts\Infra;

use Workshop\Domains\Accounts\Account;
use Workshop\Domains\Accounts\AccountState;
use Workshop\Domains\Accounts\Exceptions\CantPersistAccount;
use Workshop\Domains\Accounts\Query\Account as AccountReadModel;

class InMemoryAccountRepository implements AccountRepository
{
    private array $events = [];
    private $accounts = [];

    public function __construct()
    {
    }

    /**
     * @throws \Exception
     */
    public function getForRead(string $accountId): AccountReadModel
    {
        return AccountReadModel::fromState($this->getAccountState($accountId));
    }

    /**
     * @throws \Exception
     */
    public function getAggregate(string $accountId): Account
    {
        return Account::fromState($this->getAccountState($accountId));
    }

    public function persist(Account $account): void
    {
        if($this->accountExists($account->id) && $this->getAccountState($account->id)->version !== $account->getState()->version) {
            throw CantPersistAccount::accountChangedByOtherProcess();
        }

        foreach ($account->getUnpublishedEvents() as $event){
            $this->events[] = $event;
        }

        $this->accounts[$account->id] = $account->getState()->incrementVersion();
    }

    public function dispatchedEvents(): array
    {
        return $this->events;
    }

    public function accountExists(string $accountId): bool
    {
        return array_key_exists($accountId, $this->accounts);
    }

    /**
     * @throws \Exception
     */
    private function getAccountState(string $accountId): AccountState
    {
        if(!array_key_exists($accountId, $this->accounts)) {
            throw new \Exception('Account not found');
        }
        return $this->accounts[$accountId];
    }
}
