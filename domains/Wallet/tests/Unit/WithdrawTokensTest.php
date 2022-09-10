<?php

namespace Workshop\Domains\Wallet\Tests\Unit;

use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalRefused;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Tests\WalletTestCase;
use Workshop\Domains\Wallet\Wallet;

class WithdrawTokensTest extends WalletTestCase
{
    /** @test */
    public function it_can_withdraw_tokens_leaving_positive_balance(): void
    {
        $this->given(new TokensDeposited(100))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(50))
            ->then(new TokensWithdrawn(50));
    }

    /** @test */
    public function it_can_withdraw_tokens_leaving_zero_balance(): void
    {
        $this->given(new TokensDeposited(100))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(50) & $wallet->withdraw(50))
            ->then(new TokensWithdrawn(50), new TokensWithdrawn(50));
    }

    /** @test */
    public function it_cant_withdraw_more_tokens_than_were_deposited(): void
    {
        $this->given(new TokensDeposited(100))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(50) & $wallet->withdraw(50) & $wallet->withdraw(1))
            ->then(new TokensWithdrawn(50), new TokensWithdrawn(50), new WithdrawalRefused(1))
            ->expectToFail(SorryCantWithdraw::becauseOfInsufficientBalance(0, 1));
    }
}