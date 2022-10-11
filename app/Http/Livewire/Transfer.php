<?php

namespace App\Http\Livewire;

use League\Tactician\CommandBus;
use Livewire\Component;
use Workshop\Domains\Wallet\Commands\TransferTokens;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\WalletId;

class Transfer extends Component
{
    public $tokens;
    public $fromWalletId = 'null';
    public $toWalletId = 'null';

    protected $rules = [
        'tokens' => 'required|integer|min:1',
        'fromWalletId' => 'required|string|uuid|not_in:null',
        'toWalletId' => 'required|string|uuid|not_in:null',
    ];

    public function mount(WalletBalanceRepository $walletBalanceRepository)
    {
    }
    public function transfer(CommandBus $commandBus)
    {
        ray()->clearAll();

        if($this->fromWalletId === 'null' || $this->toWalletId === 'null'){
            session()->flash('success', 'Select wallets');
            return;
        }
        $commandBus->handle(new TransferTokens(
            transactionId: $transactionId = TransactionId::generate(),
            sendingWalletId: WalletId::fromString($this->fromWalletId),
            receivingWalletId: WalletId::fromString($this->toWalletId),
            tokens: $this->tokens,
            description: 'Transfer',
        ));

        session()->flash('success', 'Money transfer started');
    }

    public function render(WalletBalanceRepository $walletBalanceRepository)
    {
        return view('livewire.transfer', [
            'wallets' => array_keys($walletBalanceRepository->getWallets())
        ]);
    }
}
