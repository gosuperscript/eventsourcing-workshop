<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\TokensDeposited;
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
}