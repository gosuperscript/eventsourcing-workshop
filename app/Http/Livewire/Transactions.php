<?php

namespace App\Http\Livewire;

use League\Tactician\CommandBus;
use Workshop\Domains\Wallet\Commands\DepositTokens;
use Workshop\Domains\Wallet\Commands\WithdrawTokens;
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

    public function deposit(CommandBus $commandBus)
    {

        $commandBus->handle(new DepositTokens(
            walletId: WalletId::fromString($this->walletId),
            tokens: $this->tokens,
            description: $this->description
        ));

        $this->tokens = 0;
        $this->description = '';
        session()->flash('success', 'Money successfully deposited.');
    }

    public function withdraw(CommandBus $commandBus)
    {
        $commandBus->handle(new WithdrawTokens(
            walletId: WalletId::fromString($this->walletId),
            tokens: $this->tokens,
            description: $this->description
        ));

        $this->tokens = 0;
        $this->description = '';
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
