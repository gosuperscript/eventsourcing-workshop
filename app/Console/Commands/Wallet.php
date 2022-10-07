<?php

namespace App\Console\Commands;

use Assert\Assert;
use Illuminate\Console\Command;
use League\Tactician\CommandBus;
use Workshop\Domains\Wallet\Commands\DepositTokens;
use Workshop\Domains\Wallet\Commands\WithdrawTokens;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\WalletId;

class Wallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CommandBus $commandBus, WalletRepository $walletRepository)
    {
        $action = $this->choice(
            'Deposit or withdraw?',
            ['Deposit', 'Withdraw'],
        );
        if ($this->confirm('Already existing wallet?')) {
            $walletId = WalletId::fromString($this->anticipate('Whats the id of the wallet?', []));
        } else {
            $walletId = WalletId::generate();
        }

        $tokens = (int) $this->ask("amount of tokens?");
        Assert::that($tokens)->integer()->greaterThan(0);

        $wallet = $walletRepository->retrieve($walletId);
        try {
            if($action === 'Deposit'){
                $commandBus->handle(new DepositTokens($walletId, $tokens, $this->ask("description?")));
            } else {
                $commandBus->handle(new WithdrawTokens($walletId, $tokens, $this->ask("description?")));
            }
        } catch (SorryCantWithdraw $exception) {
            $this->error($exception->getMessage());
            return 1;
        } finally {
            $walletRepository->persist($wallet);
        }

        $this->info("✅ Tokens successfully {$action}d, to wallet {$walletId->toString()}");
        return 0;
    }
}
