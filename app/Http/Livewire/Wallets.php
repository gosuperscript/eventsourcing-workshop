<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;

class Wallets extends Component
{

    public function render(WalletBalanceRepository $walletBalanceRepository)
    {
        return view('livewire.wallets', [
            'wallets' => $walletBalanceRepository->getWallets()
        ]);
    }
}
