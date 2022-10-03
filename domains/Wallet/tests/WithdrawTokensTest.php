<?php

namespace Workshop\Domains\Wallet\Tests;

use Ramsey\Uuid\Uuid;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalFailed;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Wallet;

class WithdrawTokensTest extends WalletTestCase
{
    /** @test */
    public function it_can_withdraw_tokens()
    {
        $this->given(new TokensDeposited(100, 'foo', $this->now))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(100, 'foo', $this->now))
            ->then(new TokensWithdrawn(100, 'foo', $this->now));
    }

    /** @test */
    public function it_throws_an_exception_on_insufficient_funds()
    {
        $this->given(new TokensDeposited(100, 'foo', $this->now))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(101, 'foo', $this->now))
            ->expectToFail(SorryCantWithdraw::becauseOfInsufficientFunds())
            ->then(WithdrawalFailed::becauseOfInsufficientFunds());
    }
}
