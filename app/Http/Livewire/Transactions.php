<?php

namespace App\Http\Livewire;

use EventSauce\Clock\Clock;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\ReadModels\Transaction;
use Livewire\Component;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\WalletId;

class Transactions extends Component
{
    public string $walletId;

    public $tokens = 0;
    public $description = '';

    protected $rules = [
        'tokens' => 'required|integer|min:1',
    ];

    public function mount(string $walletId)
    {
        // check if wallet id can be parsed.
        WalletId::fromString($walletId);
        $this->walletId = $walletId;
    }

    public function deposit(WalletRepository $walletRepository, Clock $clock)
    {

        $wallet = $walletRepository->retrieve(WalletId::fromString($this->walletId));
        $wallet->deposit($this->tokens, $this->description, $clock->now());
        $walletRepository->persist($wallet);

        $this->tokens = 0;
        $this->description = '';
        session()->flash('success', 'Money successfully deposited.');
    }

    public function withdraw(WalletRepository $walletRepository)
    {
        $wallet = $walletRepository->retrieve(WalletId::fromString($this->walletId));
        $wallet->withdraw($this->tokens, $this->description);
        $walletRepository->persist($wallet);

        $this->tokens = 0;
        $this->description = '';
        session()->flash('success', 'Money successfully withdrawn.');
    }

    public function dismiss()
    {
        session()->forget('success');
    }

    public function render(WalletBalanceRepository $walletBalanceRepository)
    {
        return view('livewire.transactions', [
            'transactions' => Transaction::forWallet($this->walletId)->orderBy('transacted_at', 'desc')->paginate(10),
            'balance' => $walletBalanceRepository->getWalletTokens(WalletId::fromString($this->walletId))
        ]);
    }
}
