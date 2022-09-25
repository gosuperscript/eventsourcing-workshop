<?php

namespace Workshop\Domains\Wallet\Tests\Unit\Events;

use Workshop\Domains\Wallet\Tests\WalletTestCase;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\Events;

class WithdrawTokensTest extends WalletTestCase
{
    public function test_withdraw_triggers_event()
    {
        $this->given(new Events\TokensDeposited(200))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(100))
            ->then(new Events\TokensWithdrawn(100));
    }
}