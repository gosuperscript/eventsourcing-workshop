<?php

namespace Workshop\Domains\Wallet\Tests;

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
            ->when(fn(Wallet $wallet) => $wallet->deposit(100))
            ->then(new TokensDeposited(100));
    }

    /** @test */
    public function it_can_withdraw_tokens()
    {
        $this->given(new TokensDeposited(100))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(100))
            ->then(new TokensWithdrawn(100));
    }

    /** @test */
    public function it_throws_an_exception_on_insufficient_funds()
    {
        $this->given(new TokensDeposited(100))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(101))
            ->expectToFail(SorryCantWithdraw::becauseOfInsufficientFunds())
            ->then(WithdrawalFailed::becauseOfInsufficientFunds());
    }
}
