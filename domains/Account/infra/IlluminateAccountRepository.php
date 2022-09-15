<?php

namespace Workshop\Domains\Accounts\Infra;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;
use Workshop\Domains\Accounts\Account;
use Workshop\Domains\Accounts\AccountState;
use Workshop\Domains\Accounts\Exceptions\CantPersistAccount;
use Workshop\Domains\Accounts\Query\Account as AccountReadModel;

class IlluminateAccountRepository implements AccountRepository
{
    public function __construct(
        private ConnectionInterface $connection,
        private string $tableName,
        private Dispatcher $dispatcher,
    )
    {
    }

    public function getForRead(string $accountId): AccountReadModel
    {
        return AccountReadModel::fromState($this->getAccountState($accountId));
    }

    public function getAggregate(string $accountId): Account
    {
        return Account::fromState($this->getAccountState($accountId));
    }

    public function persist(Account $account): void
    {
        $row = $this->connection->table($this->tableName)->where('id', $account->id)->first();
        $state = $account->getState();
        if($row !== null && $state->version !== $row->version) {
            throw CantPersistAccount::accountChangedByOtherProcess();
        }
        $events = $account->getUnpublishedEvents();
        if($row === null){
            $this->persistAccountState($state);
        }else{
            $this->updateAccountState($state);
        }
        foreach ($events as $event){
            $this->dispatcher->dispatch($event);
        }
    }

    private function persistAccountState(\Workshop\Domains\Accounts\AccountState $state)
    {
        $state = $state->incrementVersion();
        $this->connection->table($this->tableName)->insert([
            'id' => $state->accountId,
            'name' => $state->name,
            'email' => $state->email,
            'verified' => $state->verified,
            'version' => $state->version,
        ]);
    }

    private function updateAccountState(\Workshop\Domains\Accounts\AccountState $state)
    {
        $state = $state->incrementVersion();
        $this->connection->table($this->tableName)->where('id', $state->accountId)->update([
            'name' => $state->name,
            'email' => $state->email,
            'verified' => $state->verified,
            'version' => $state->version,
        ]);
    }

    private function getAccountState(string $accountId): AccountState
    {
        $row = $this->connection->table($this->tableName)->where('id', $accountId)->first();
        return new AccountState(
            $row->id,
            $row->version,
            $row->email,
            $row->name,
            $row->verified
        );
    }
}
