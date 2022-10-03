<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\Events\TransferInitiated;
use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

class WalletTransferTest extends WalletTestCase
{
    /** @test */
    public function wallet_can_give_assignment_to_transfer_funds_to_other_wallet()
    {
        $transactionId = TransactionId::generate();
        $receivingWalletId = WalletId::generate();

        $this->givenTokensDeposited(100)
            ->when(fn(Wallet $wallet) => $wallet->transfer($transactionId, $receivingWalletId, 100, 'foo', $this->now))
            ->then(new TransferInitiated($transactionId, $receivingWalletId, 100, 'foo', $this->now));
    }

    /** @test */
    public function on_not_enough_tokens_it_wouldnt_start_a_transfer()
    {
        $transactionId = TransactionId::generate();
        $receivingWalletId = WalletId::generate();

        $this->givenTokensDeposited(10)
            ->when(fn(Wallet $wallet) => $wallet->transfer($transactionId, $receivingWalletId, 100, 'foo', $this->now))
            ->thenNothingShouldHaveHappened();
    }
}
