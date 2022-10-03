<?php

namespace Workshop\Domains\Wallet\Tests;

use DateTimeImmutable;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalFailed;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Wallet;

class DepositTokensTest extends WalletTestCase
{
    /** @test */
    public function it_can_deposit_tokens()
    {
        $this->given()
            ->when(fn(Wallet $wallet) => $wallet->deposit(100, 'foo', $this->now))
            ->then(new TokensDeposited(100, 'foo', $this->now));
    }
}
