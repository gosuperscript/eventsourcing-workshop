<?php

namespace Workshop\Domains\Wallet\Tests\Unit\Events;

use Workshop\Domains\Wallet\Tests\WalletTestCase;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\Events;

class TokensOverdrawAttemptedTest extends WalletTestCase
{
    public function test_withdraw_triggers_event()
    {
        $this->given()
            ->when(fn(Wallet $wallet) => $wallet->withdraw(100))
            ->then(new Events\TokensOverdrawAttempted(time()));
    }
}