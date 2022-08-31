<?php

namespace Workshop\Domains\Accounts\Tests;

use PHPUnit\Framework\TestCase;
use Workshop\Domains\Accounts\Account;
use Workshop\Domains\Accounts\AccountState;
use Workshop\Domains\Accounts\Commands\OpenAccount;
use Workshop\Domains\Accounts\Events\AccountOpened;

class OpenAccountTest extends TestCase
{
    private string $accountId;

    public function setUp(): void
    {
        $this->accountId = 'foo';
    }

    /** @test */
    public function a_bank_account_can_be_opened_using_a_valid_command()
    {
        $account = Account::open(new OpenAccount($this->accountId, 'john@doe.nl', 'John Doe'));
        $this->assertEquals(new AccountState(
            $this->accountId,
            email: 'john@doe.nl',
            name: 'John Doe',
        ), $account->getState());

        $this->assertEquals([
            new AccountOpened($this->accountId, 'John Doe', 'john@doe.nl'),
        ], $account->getUnpublishedEvents());
    }
}
