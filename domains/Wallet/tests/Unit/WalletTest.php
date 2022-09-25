<?php

namespace Workshop\Domains\Wallet\Tests\Unit;

use Workshop\Domains\Wallet\Tests\WalletTestCase;

class WalletTest extends WalletTestCase
{
    public function test_it_is_created_with_0_tokens()
    {
        $wallet = $this->repository->retrieve($this->aggregateRootId);

        $this->assertEquals(0, $wallet->tokens);
    }

    public function test_deposit_adds_tokens()
    {
        $wallet = $this->repository->retrieve($this->aggregateRootId);
        $wallet->deposit(100);
        $this->assertEquals(100, $wallet->tokens);
    }

    public function test_withdraw_does_not_substract_tokens()
    {
        $wallet = $this->repository->retrieve($this->aggregateRootId);
        $wallet->deposit(50)->withdraw(100);
        $this->assertEquals(50, $wallet->tokens);
    }

    public function test_withdraw_substracts_tokens()
    {
        $wallet = $this->repository->retrieve($this->aggregateRootId);
        $wallet->deposit(200)->withdraw(100);
        $this->assertEquals(100, $wallet->tokens);
    }
}