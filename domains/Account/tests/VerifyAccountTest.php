<?php

namespace Workshop\Domains\Accounts\Tests;

use PHPUnit\Framework\TestCase;
use Workshop\Domains\Accounts\Account;
use Workshop\Domains\Accounts\AccountState;
use Workshop\Domains\Accounts\Commands\OpenAccount;
use Workshop\Domains\Accounts\Events\AccountOpened;
use Workshop\Domains\Accounts\Events\AccountVerified;
use Workshop\Domains\Accounts\Exceptions\SorryCantVerifyAccount;
use Workshop\Domains\Accounts\Exceptions\SorryCantVerifyAccountTwice;

class VerifyAccountTest extends AccountTestCase
{

    /** @test */
    public function a_bank_account_can_be_verified()
    {
        $this->given(new AccountState($this->accountId, email: 'john@doe.nl', name: 'John Doe', verified: false));
        $this->when(fn(Account $account) => $account->verify());
        $this->then(function (AccountState $accountState){
            $this->assertTrue($accountState->verified);
        });
        $this->assertDispatched(new AccountVerified($this->accountId));
    }

    /** @test */
    public function a_bank_account_cant_be_verified_twice()
    {
        $this->given(new AccountState($this->accountId, email: 'john@doe.nl', name: 'John Doe', verified: true));
        $this->expectExceptionObject(SorryCantVerifyAccount::alreadyVerified());
        $this->when(fn(Account $account) => $account->verify());
    }
}
