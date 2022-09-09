<?php

namespace Workshop\Domains\Wallet\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\RandomNumberMessageHeaderAdder;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

class WalletInfraTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_persist_wallets(): void
    {
        /** @var WalletRepository $walletRepo */
        $walletRepo = app(WalletRepository::class);
        /** @var Wallet $wallet */
        $wallet = $walletRepo->retrieve(WalletId::generate());
        $wallet->deposit(100);
        $walletRepo->persist($wallet);

        /** @var Wallet $reloadedWallet */
        $reloadedWallet = $walletRepo->retrieve($wallet->aggregateRootId());
        $reloadedWallet->withdraw(99);
        $this->expectException(SorryCantWithdraw::class);
        $reloadedWallet->withdraw(2);
    }

    /** @test */
    public function it_adds_a_random_number_header(): void
    {
        /** @var WalletRepository $walletRepo */
        $walletRepo = app(WalletRepository::class);
        /** @var Wallet $wallet */
        $wallet = $walletRepo->retrieve(WalletId::generate());
        $wallet->deposit(100);
        $walletRepo->persist($wallet);

        $this->assertIsNumeric(
            IntegrationTestMessageDispatcher::instance()->received()[0]->header(RandomNumberMessageHeaderAdder::KEY)
        );
    }
}