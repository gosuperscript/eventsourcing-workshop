<?php

namespace App\Http\Livewire;

use Workshop\Domains\Wallet\ReadModels\Transaction;
use Livewire\Component;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\WalletId;

class Transactions extends Component
{
    public string $walletId;

    public $tokens = 0;

    protected $rules = [
        'tokens' => 'required|integer|min:1',
    ];

    public function mount(string $walletId)
    {
        // check if wallet id can be parsed.
        WalletId::fromString($walletId);
        $this->walletId = $walletId;
    }

    public function deposit(WalletRepository $walletRepository)
    {

        $wallet = $walletRepository->retrieve(WalletId::fromString($this->walletId));
        $wallet->deposit($this->tokens);
        $walletRepository->persist($wallet);

        $this->tokens = 0;
        session()->flash('success', 'Money successfully deposited.');
    }

    public function withdraw(WalletRepository $walletRepository)
    {
        $wallet = $walletRepository->retrieve(WalletId::fromString($this->walletId));
        $wallet->withdraw($this->tokens);
        $walletRepository->persist($wallet);

        $this->tokens = 0;
        session()->flash('success', 'Money successfully withdrawn.');
    }

    public function dismiss()
    {
        session()->forget('success');
    }

    public function render()
    {
        return view('livewire.transactions', [
            'transactions' => Transaction::forWallet($this->walletId)->orderBy('transacted_at', 'desc')->paginate(10),
        ]);
    }
}
