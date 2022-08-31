<?php

namespace Workshop\Domains\Accounts\Infra;

use Workshop\Domains\Accounts\Account;
use Workshop\Domains\Accounts\Query\Account as AccountReadModel;

interface AccountRepository
{
    public function getForRead(string $accountId): AccountReadModel;

    public function getAggregate(string $accountId): Account;

    public function persist(Account $account): void;
}
