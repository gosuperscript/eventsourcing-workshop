<?php

namespace Workshop\Domains\Accounts;

use Workshop\Domains\Accounts\Commands\OpenAccount;
use Workshop\Domains\Accounts\Commands\VerifyAccount;
use Workshop\Domains\Accounts\Infra\AccountRepository;

final class AccountCommandHandler
{
    public function __construct(
        private AccountRepository $accountRepository
    )
    {
    }

    public function handleCreateAccount(OpenAccount $openAccount): void
    {
        $account = Account::open($openAccount);
        $this->accountRepository->persist($account);
    }

    /**
     * @throws Exceptions\SorryCantVerifyAccount
     */
    public function handleVerifyAccount(VerifyAccount $verifyAccount): void
    {
        $account = $this->accountRepository->getAggregate($verifyAccount->accountId);
        $account->verify();
        $this->accountRepository->persist($account);
    }
}
