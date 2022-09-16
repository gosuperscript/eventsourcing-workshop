<?php

namespace Workshop\Domains\Wallet\Tests\Infra;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Workshop\Domains\Wallet\Infra\IlluminateWalletBalanceRepository;
use Workshop\Domains\Wallet\WalletId;

class WalletBalanceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_new_wallet()
    {
        $walletId = WalletId::generate();
        $repo = new IlluminateWalletBalanceRepository();
        $repo->updateWalletTokens($walletId, 10);

        $this->assertEquals(10, $repo->getWalletTokens($walletId));
        $this->assertEquals([$walletId->toString() => 10], $repo->getWallets());
    }

    /** @test */
    public function it_can_update_a_wallet()
    {
        $walletId = WalletId::generate();
        $repo = new IlluminateWalletBalanceRepository();
        $repo->updateWalletTokens($walletId, 10);
        $repo->updateWalletTokens($walletId, 20);

        $this->assertEquals(20, $repo->getWalletTokens($walletId));
        $this->assertEquals([$walletId->toString() => 20], $repo->getWallets());
    }
}
