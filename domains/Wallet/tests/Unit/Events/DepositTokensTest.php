<?php

namespace Workshop\Domains\Wallet\Tests\Unit\Events;

use Workshop\Domains\Wallet\Tests\WalletTestCase;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\Events;

class DepositTokensTest extends WalletTestCase
{
    public function test_deposit_triggers_event()
    {
        $this->given()
            ->when(fn(Wallet $wallet) => $wallet->deposit(100))
            ->then(new Events\TokensDeposited(100));
    }
}